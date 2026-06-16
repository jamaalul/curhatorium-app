<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_order()
    {
        $plan = MembershipPlan::factory()->create();

        $response = $this->post(route('order.create', $plan));

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_order_for_membership_plan()
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['price_idr' => 100000]);

        $response = $this->actingAs($user)->post(route('order.create', $plan));

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals($plan->id, $order->orderable_id);
        $this->assertEquals(MembershipPlan::class, $order->orderable_type);
        $this->assertEquals(100000, $order->gross_amount);
        $this->assertEquals('pending', $order->status);

        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertEquals($order->id, $payment->order_id);
        $this->assertNotEmpty($payment->midtrans_transaction_id);
        $this->assertEquals('pending', $payment->transaction_status);
        $this->assertNotEmpty($payment->qris_url);

        $response->assertRedirect(route('order.show', $order));
    }

    public function test_user_can_view_order_checkout_page()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->pending()->create();
        $payment = Payment::factory()->for($order)->pending()->create([
            'qris_url' => 'https://example.com/qr.png',
        ]);

        $response = $this->actingAs($user)->get(route('order.show', $order));

        $response->assertOk();
        $response->assertSee($order->order_ref);
        $response->assertSee('https://example.com/qr.png');
    }

    public function test_order_status_check_endpoint()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->paid()->create();
        $payment = Payment::factory()->for($order)->settled()->create();

        $response = $this->actingAs($user)->get(route('order.check-status', $order));

        $response->assertOk();
        $response->assertJson([
            'order_status' => 'paid',
            'payment_status' => 'settlement',
        ]);
    }

    public function test_midtrans_webhook_settlement_updates_status()
    {
        $order = Order::factory()->pending()->create();
        $payment = Payment::factory()->for($order)->pending()->create();

        $payload = [
            'order_id' => $order->order_ref,
            'status_code' => '200',
            'gross_amount' => number_format($order->gross_amount, 2, '.', ''),
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'trans-123',
        ];

        // Generate valid signature
        $serverKey = config('midtrans.server_key');
        $signatureStr = $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey;
        $payload['signature_key'] = hash('sha512', $signatureStr);

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertOk();

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('settlement', $payment->fresh()->transaction_status);
        $this->assertEquals('qris', $payment->fresh()->payment_type);
    }

    public function test_midtrans_webhook_rejects_invalid_signature()
    {
        $order = Order::factory()->pending()->create();

        $payload = [
            'order_id' => $order->order_ref,
            'status_code' => '200',
            'gross_amount' => '100.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'invalid-signature',
        ];

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Invalid signature']);
    }
}
