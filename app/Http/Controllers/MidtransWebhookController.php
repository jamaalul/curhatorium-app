<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    /**
     * Handle incoming Midtrans payment notification (webhook).
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans notification received', ['payload' => $payload]);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (! $orderId || ! $signatureKey) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        if (! $this->midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans notification signature verification failed', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('order_ref', $orderId)->first();

        if (! $order) {
            Log::warning('Midtrans notification for unknown order', ['order_id' => $orderId]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update the latest payment record
        $payment = $order->latestPayment;

        if ($payment) {
            $payment->update([
                'midtrans_transaction_id' => $transactionId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'midtrans_response' => $payload,
            ]);
        }

        // Map Midtrans transaction status to order status
        $orderStatus = match ($transactionStatus) {
            'settlement' => 'paid',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            'deny' => 'cancelled',
            default => $order->status,
        };

        if ($order->status !== $orderStatus) {
            $order->update(['status' => $orderStatus]);

            if ($orderStatus === 'paid') {
                OrderPaid::dispatch($order);
            }
        }

        Log::info('Midtrans notification processed', [
            'order_ref' => $orderId,
            'transaction_status' => $transactionStatus,
            'order_status' => $orderStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }
}
