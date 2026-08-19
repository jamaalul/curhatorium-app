<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\MembershipPlan;
use App\Models\Order;
use App\Services\DokuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private DokuService $doku,
    ) {}

    /**
     * Create an order for a membership plan, charge via DOKU QRIS, and redirect to checkout.
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

            $chargeResult = $this->doku->chargeQris($order);

            $order->payments()->create([
                'payment_transaction_id' => $chargeResult['transaction_id'],
                'gross_amount' => $chargeResult['gross_amount'],
                'currency' => 'IDR',
                'payment_type' => $chargeResult['payment_type'],
                'transaction_status' => $chargeResult['transaction_status'],
                'qris_url' => $chargeResult['qr_code_url'],
                'payment_gateway_response' => $chargeResult['raw'],
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
     * Queries transaction status from DOKU API directly.
     */
    public function checkStatus(Order $order): JsonResponse
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        $latestPayment = $order->latestPayment;

        if ($latestPayment && $latestPayment->payment_transaction_id && $order->isPending()) {
            try {
                $queryResult = $this->doku->queryQris(
                    $latestPayment->payment_transaction_id,
                    $order->order_ref
                );

                $latestStatus = (string) ($queryResult['latestTransactionStatus'] ?? '');

                // Map DOKU latestTransactionStatus
                $transactionStatus = match ($latestStatus) {
                    '00', 'PAID', 'SUCCESS' => 'settlement',
                    '05', 'EXPIRED' => 'expire',
                    'CANCELLED' => 'cancel',
                    default => $latestPayment->transaction_status,
                };

                // Update payment status
                $latestPayment->update([
                    'transaction_status' => $transactionStatus,
                ]);

                // Map to order status
                $orderStatus = match ($transactionStatus) {
                    'settlement' => 'paid',
                    'expire' => 'expired',
                    'cancel', 'deny' => 'cancelled',
                    default => $order->status,
                };

                if ($order->status !== $orderStatus) {
                    $order->update(['status' => $orderStatus]);

                    if ($orderStatus === 'paid') {
                        OrderPaid::dispatch($order);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to check DOKU transaction status inline', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'order_status' => $order->fresh()->status,
            'payment_status' => $latestPayment?->fresh()->transaction_status,
        ]);
    }
}
