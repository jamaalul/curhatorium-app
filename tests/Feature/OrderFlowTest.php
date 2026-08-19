<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\DokuService;
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
        $this->mock(DokuService::class, function ($mock): void {
            $mock->shouldReceive('chargeQris')
                ->once()
                ->andReturn([
                    'transaction_id' => 'DOKU-TRANS-12345',
                    'order_id' => 'ORD-123',
                    'gross_amount' => '100000.00',
                    'payment_type' => 'qris',
                    'transaction_status' => 'pending',
                    'qr_code_url' => '00020101021226590014ID.DOKU.WWW0118936009140000000000',
                    'deeplink_url' => null,
                    'actions' => [],
                    'raw' => [
                        'responseCode' => '2000000',
                        'responseMessage' => 'Successful',
                        'referenceNo' => 'DOKU-TRANS-12345',
                        'partnerReferenceNo' => 'ORD-123',
                        'qrContent' => '00020101021226590014ID.DOKU.WWW0118936009140000000000',
                    ],
                ]);
        });

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
        $this->assertEquals('DOKU-TRANS-12345', $payment->payment_transaction_id);
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

    public function test_user_can_view_order_checkout_page_with_raw_qr_string()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->pending()->create();
        $rawQr = '00020101021226540012COM.DOKU.WWW01189360089900000252800205252800303UKE51440014ID.CO.QRIS.WWW0215ID20260818785180303UKE520456615303360540849900.005802ID5910tlfnhNLAEQ6015JAKARTA SELATAN61056011562320706DELT015018ORD-20260819-61F1E6304E0A0';
        $payment = Payment::factory()->for($order)->pending()->create([
            'qris_url' => $rawQr,
        ]);

        $response = $this->actingAs($user)->get(route('order.show', $order));

        $response->assertOk();
        $response->assertSee($order->order_ref);
        $response->assertDontSee('src="'.$rawQr.'"', false);
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

    public function test_doku_webhook_settlement_updates_status()
    {
        $order = Order::factory()->pending()->create();
        $payment = Payment::factory()->for($order)->pending()->create();

        $payload = [
            'originalPartnerReferenceNo' => $order->order_ref,
            'originalReferenceNo' => 'DOKU-TRANS-999',
            'latestTransactionStatus' => '00',
            'amount' => [
                'value' => number_format($order->gross_amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertOk();
        $response->assertJson(['responseCode' => '2000000']);

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('settlement', $payment->fresh()->transaction_status);
        $this->assertEquals('qris', $payment->fresh()->payment_type);
        $this->assertEquals('DOKU-TRANS-999', $payment->fresh()->payment_transaction_id);
    }

    public function test_doku_webhook_rejects_missing_order_ref()
    {
        $payload = [
            'latestTransactionStatus' => '00',
        ];

        $response = $this->postJson(route('doku.notification'), $payload);

        $response->assertStatus(400);
        $response->assertJson(['responseCode' => '4000000']);
    }
}
