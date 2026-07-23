<?php

namespace Tests\Feature\Ebook;

use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\EbookComment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EbookModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ebook_model_relationships_work(): void
    {
        $user = User::factory()->create();
        $category = EbookCategory::query()->create([
            'name' => 'Mindfulness',
            'slug' => 'mindfulness',
        ]);
        $ebook = Ebook::query()->create([
            'ebook_category_id' => $category->getKey(),
            'title' => 'Panduan Mindfulness',
            'slug' => 'panduan-mindfulness',
            'description' => 'Panduan praktik mindfulness.',
            'cover_image' => 'ebooks/covers/mindfulness.jpg',
            'file_url' => 'ebooks/files/mindfulness.pdf',
            'price' => 75000,
            'page_count' => 42,
            'is_published' => true,
        ]);
        $comment = EbookComment::query()->create([
            'ebook_id' => $ebook->getKey(),
            'user_id' => $user->getKey(),
            'content' => 'Materinya membantu.',
            'is_visible' => true,
        ]);

        $this->assertTrue($category->ebooks->first()->is($ebook));
        $this->assertTrue($ebook->category->is($category));
        $this->assertTrue($ebook->comments->first()->is($comment));
        $this->assertTrue($comment->ebook->is($ebook));
        $this->assertTrue($comment->user->is($user));
        $this->assertTrue($ebook->is_published);
        $this->assertSame(42, $ebook->page_count);
        $this->assertSame('75000.00', $ebook->price);
        $this->assertSame('Panduan Mindfulness', $ebook->name);
    }

    public function test_published_scope_only_returns_published_ebooks(): void
    {
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

        $ebooks = Ebook::query()->published()->get();

        $this->assertTrue($ebooks->contains($published));
        $this->assertFalse($ebooks->contains($draft));
    }

    public function test_ebook_uses_slug_for_route_binding_and_can_have_orders(): void
    {
        $user = User::factory()->create();
        $ebook = $this->createEbook();

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

        $this->assertSame('slug', $ebook->getRouteKeyName());
        $this->assertTrue($ebook->orders->first()->is($order));
        $this->assertTrue($order->orderable->is($ebook));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createEbook(array $attributes = []): Ebook
    {
        return Ebook::query()->create(array_merge([
            'title' => 'Ebook Curhatorium',
            'slug' => 'ebook-curhatorium-'.Ebook::query()->count(),
            'description' => 'Konten ebook Curhatorium.',
            'price' => 99000,
            'page_count' => 30,
            'is_published' => true,
        ], $attributes));
    }
}
