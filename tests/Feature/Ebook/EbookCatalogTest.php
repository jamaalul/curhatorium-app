<?php

namespace Tests\Feature\Ebook;

use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_only_shows_published_ebooks(): void
    {
        $this->withoutVite();

        $published = $this->createEbook([
            'title' => 'Ebook Published',
            'slug' => 'ebook-published',
            'is_published' => true,
        ]);
        $draft = $this->createEbook([
            'title' => 'Ebook Draft',
            'slug' => 'ebook-draft',
            'is_published' => false,
        ]);

        $response = $this->get(route('ebooks.index'));

        $response->assertOk();
        $response->assertSee($published->title);
        $response->assertDontSee($draft->title);
    }

    public function test_published_ebook_detail_can_be_opened_by_slug_without_exposing_file_url(): void
    {
        $this->withoutVite();

        $ebook = $this->createEbook([
            'title' => 'Panduan Mindfulness',
            'slug' => 'panduan-mindfulness',
            'file_url' => 'ebooks/files/private-mindfulness.pdf',
            'is_published' => true,
        ]);

        $response = $this->get(route('ebooks.show', $ebook));

        $response->assertOk();
        $response->assertSee('Panduan Mindfulness');
        $response->assertDontSee('ebooks/files/private-mindfulness.pdf');
    }

    public function test_draft_and_invalid_ebook_detail_return_404(): void
    {
        $this->withoutVite();

        $draft = $this->createEbook([
            'title' => 'Ebook Draft',
            'slug' => 'ebook-draft',
            'is_published' => false,
        ]);

        $this->get(route('ebooks.show', $draft))->assertNotFound();
        $this->get('/ebooks/slug-tidak-ada')->assertNotFound();
    }

    public function test_guest_cannot_checkout_ebook(): void
    {
        $ebook = $this->createEbook([
            'slug' => 'ebook-checkout-guest',
            'is_published' => true,
        ]);

        $this->post(route('ebooks.checkout', $ebook))
            ->assertRedirect('/login');
    }

    public function test_user_can_create_ebook_order_with_existing_order_system(): void
    {
        $this->mock(MidtransService::class, function ($mock): void {
            $mock->shouldReceive('chargeQris')
                ->once()
                ->andReturn([
                    'transaction_id' => 'ebook-transaction-1',
                    'order_id' => 'midtrans-order-1',
                    'gross_amount' => '99000.00',
                    'payment_type' => 'qris',
                    'transaction_status' => 'pending',
                    'qr_code_url' => 'https://example.com/qr.png',
                    'deeplink_url' => null,
                    'actions' => [],
                    'raw' => (object) [
                        'transaction_id' => 'ebook-transaction-1',
                        'transaction_status' => 'pending',
                    ],
                ]);
        });

        $user = User::factory()->create();
        $ebook = $this->createEbook([
            'title' => 'Ebook Checkout',
            'slug' => 'ebook-checkout',
            'price' => 99000,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->post(route('ebooks.checkout', $ebook));

        $order = Order::query()->first();
        $payment = Payment::query()->first();

        $this->assertNotNull($order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(Ebook::class, $order->orderable_type);
        $this->assertEquals($ebook->id, $order->orderable_id);
        $this->assertEquals('99000.00', $order->gross_amount);
        $this->assertEquals('pending', $order->status);

        $this->assertNotNull($payment);
        $this->assertEquals($order->id, $payment->order_id);
        $this->assertEquals('ebook-transaction-1', $payment->midtrans_transaction_id);
        $this->assertEquals('qris', $payment->payment_type);
        $this->assertEquals('pending', $payment->transaction_status);

        $response->assertRedirect(route('order.show', $order));
    }

    public function test_checkout_reuses_existing_pending_ebook_order(): void
    {
        $user = User::factory()->create();
        $ebook = $this->createEbook([
            'slug' => 'ebook-pending-order',
            'is_published' => true,
        ]);
        $order = Order::query()->create([
            'order_ref' => Order::generateOrderRef(),
            'user_id' => $user->getKey(),
            'orderable_type' => Ebook::class,
            'orderable_id' => $ebook->getKey(),
            'quantity' => 1,
            'unit_price' => $ebook->price,
            'gross_amount' => $ebook->price,
            'status' => 'pending',
            'expired_at' => now()->addMinutes(15),
        ]);

        $this->mock(MidtransService::class, function ($mock): void {
            $mock->shouldNotReceive('chargeQris');
        });

        $this->actingAs($user)
            ->post(route('ebooks.checkout', $ebook))
            ->assertRedirect(route('order.show', $order));

        $this->assertSame(1, Order::query()->count());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createEbook(array $attributes = []): Ebook
    {
        $category = EbookCategory::query()->first()
            ?? EbookCategory::query()->create([
                'name' => 'Kategori Ebook',
                'slug' => 'kategori-ebook',
            ]);

        return Ebook::query()->create(array_merge([
            'ebook_category_id' => $category->getKey(),
            'title' => 'Ebook Curhatorium',
            'slug' => 'ebook-curhatorium-'.Ebook::query()->count(),
            'description' => 'Konten ebook Curhatorium.',
            'price' => 99000,
            'page_count' => 30,
            'file_url' => 'ebooks/files/ebook-curhatorium.pdf',
            'is_published' => true,
        ], $attributes));
    }
}
