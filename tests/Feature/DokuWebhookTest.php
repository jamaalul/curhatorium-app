<?php

namespace Tests\Feature;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DokuWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_doku_webhook_paid_updates_order_to_paid_and_dispatches_event(): void
    {
        Event::fake([OrderPaid::class]);

        $order = Order::factory()->pending()->create();
        $payment = Payment::factory()->for($order)->pending()->create([
            'payment_transaction_id' => 'DOKU-ORIG-123',
        ]);

        $payload = [
            'originalPartnerReferenceNo' => $order->order_ref,
            'originalReferenceNo' => 'DOKU-NOTIF-456',
            'latestTransactionStatus' => '00',
            'amount' => [
                'value' => number_format($order->gross_amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertOk()
            ->assertJson([
                'responseCode' => '2000000',
                'responseMessage' => 'SUCCESS',
            ]);

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('settlement', $payment->fresh()->transaction_status);
        $this->assertEquals('DOKU-NOTIF-456', $payment->fresh()->payment_transaction_id);

        Event::assertDispatched(OrderPaid::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }

    public function test_doku_webhook_expired_updates_order_to_expired(): void
    {
        $order = Order::factory()->pending()->create();
        $payment = Payment::factory()->for($order)->pending()->create();

        $payload = [
            'originalPartnerReferenceNo' => $order->order_ref,
            'originalReferenceNo' => 'DOKU-EXP-123',
            'latestTransactionStatus' => '05',
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertOk();

        $this->assertEquals('expired', $order->fresh()->status);
        $this->assertEquals('expire', $payment->fresh()->transaction_status);
    }

    public function test_doku_webhook_cancelled_updates_order_to_cancelled(): void
    {
        $order = Order::factory()->pending()->create();
        $payment = Payment::factory()->for($order)->pending()->create();

        $payload = [
            'originalPartnerReferenceNo' => $order->order_ref,
            'originalReferenceNo' => 'DOKU-CANCEL-123',
            'latestTransactionStatus' => 'CANCELLED',
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertOk();

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals('cancel', $payment->fresh()->transaction_status);
    }

    public function test_doku_webhook_rejects_missing_order_reference(): void
    {
        $payload = [
            'latestTransactionStatus' => '00',
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertStatus(400)
            ->assertJson(['responseCode' => '4000000']);
    }

    public function test_doku_webhook_returns_404_for_unknown_order(): void
    {
        $payload = [
            'originalPartnerReferenceNo' => 'NON-EXISTENT-ORDER-REF',
            'latestTransactionStatus' => '00',
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertStatus(404)
            ->assertJson(['responseCode' => '4040000']);
    }
}
