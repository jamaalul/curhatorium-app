<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    /**
     * Handle incoming DOKU payment notification (webhook).
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('DOKU notification received', [
            'headers' => $request->headers->all(),
            'payload' => $payload,
        ]);

        $orderRef = $payload['originalPartnerReferenceNo']
            ?? $payload['partnerReferenceNo']
            ?? $payload['order_id']
            ?? null;

        $transactionId = $payload['originalReferenceNo']
            ?? $payload['referenceNo']
            ?? $payload['transaction_id']
            ?? null;

        $rawStatus = (string) (
            $payload['latestTransactionStatus']
            ?? $payload['transactionStatus']
            ?? $payload['status']
            ?? $payload['result']
            ?? ''
        );

        if (! $orderRef) {
            Log::warning('DOKU notification missing order reference', ['payload' => $payload]);

            return response()->json(['responseCode' => '4000000', 'responseMessage' => 'Invalid payload: missing order reference'], 400);
        }

        $order = Order::where('order_ref', $orderRef)->first();

        if (! $order) {
            Log::warning('DOKU notification for unknown order', ['order_ref' => $orderRef]);

            return response()->json(['responseCode' => '4040000', 'responseMessage' => 'Order not found'], 404);
        }

        // Map status
        $transactionStatus = match ($rawStatus) {
            '00', 'PAID', 'SUCCESS', 'settlement' => 'settlement',
            '05', 'EXPIRED', 'expire' => 'expire',
            'CANCELLED', 'cancel', 'deny' => 'cancel',
            default => 'pending',
        };

        // Update latest payment record if it exists
        $payment = $order->latestPayment;

        if ($payment) {
            $payment->update([
                'payment_transaction_id' => $transactionId ?? $payment->payment_transaction_id,
                'transaction_status' => $transactionStatus,
                'payment_type' => 'qris',
                'payment_gateway_response' => $payload,
            ]);
        }

        // Map to Order status
        $orderStatus = match ($transactionStatus) {
            'settlement' => 'paid',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            default => $order->status,
        };

        if ($order->status !== $orderStatus) {
            $order->update(['status' => $orderStatus]);

            if ($orderStatus === 'paid') {
                OrderPaid::dispatch($order);
            }
        }

        Log::info('DOKU notification processed', [
            'order_ref' => $orderRef,
            'transaction_status' => $transactionStatus,
            'order_status' => $orderStatus,
        ]);

        return response()->json([
            'responseCode' => '2000000',
            'responseMessage' => 'SUCCESS',
        ]);
    }
}
