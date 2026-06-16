<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Log;

class OrderController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    /**
     * Create an order for a membership plan, charge via Midtrans QRIS, and redirect to checkout.
     */
    public function create(MembershipPlan $plan): RedirectResponse
    {
        $user = Auth::user();

        // Prevent duplicate pending orders for the same plan
        $existingOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('orderable_type', MembershipPlan::class)
            ->where('orderable_id', $plan->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        if ($existingOrder) {
            return redirect()->route('order.show', $existingOrder);
        }

        $order = DB::transaction(function () use ($user, $plan) {
            $order = Order::create([
                'order_ref' => Order::generateOrderRef(),
                'user_id' => $user->id,
                'orderable_type' => MembershipPlan::class,
                'orderable_id' => $plan->id,
                'quantity' => 1,
                'unit_price' => $plan->price_idr,
                'gross_amount' => $plan->price_idr,
                'status' => 'pending',
                'expired_at' => now()->addMinutes(15),
            ]);

            $chargeResult = $this->midtrans->chargeQris($order);

            $order->payments()->create([
                'midtrans_transaction_id' => $chargeResult['transaction_id'],
                'gross_amount' => $chargeResult['gross_amount'],
                'currency' => 'IDR',
                'payment_type' => $chargeResult['payment_type'],
                'transaction_status' => $chargeResult['transaction_status'],
                'qris_url' => $chargeResult['qr_code_url'],
                'midtrans_response' => $chargeResult['raw'],
                'transaction_time' => now(),
                'expired_at' => now()->addMinutes(15),
            ]);

            return $order;
        });

        return redirect()->route('order.show', $order);
    }

    /**
     * Show the checkout / payment page with QR code.
     */
    public function show(Order $order): View|RedirectResponse
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        $order->load(['orderable', 'payments']);
        $latestPayment = $order->latestPayment;

        return view('order.show', compact('order', 'latestPayment'));
    }

    /**
     * AJAX endpoint: check current order/payment status.
     * It pulls the latest status from Midtrans to support local development without ngrok webhooks.
     */
    public function checkStatus(Order $order): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        $latestPayment = $order->latestPayment;

        if ($latestPayment && $latestPayment->midtrans_transaction_id && $order->isPending()) {
            try {
                $status = $this->midtrans->getTransactionStatus($latestPayment->midtrans_transaction_id);

                // Update payment status
                $latestPayment->update([
                    'transaction_status' => $status->transaction_status,
                    'payment_type' => $status->payment_type ?? $latestPayment->payment_type,
                ]);

                // Map Midtrans transaction status to order status
                $orderStatus = match ($status->transaction_status) {
                    'settlement' => 'paid',
                    'expire' => 'expired',
                    'cancel', 'deny' => 'cancelled',
                    default => $order->status,
                };

                if ($order->status !== $orderStatus) {
                    $order->update(['status' => $orderStatus]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to check Midtrans transaction status inline', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'order_status' => $order->fresh()->status,
            'payment_status' => $latestPayment?->fresh()->transaction_status,
        ]);
    }
}
