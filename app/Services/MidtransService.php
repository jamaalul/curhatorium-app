<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config as MidtransConfig;
use Midtrans\CoreApi;
use Midtrans\Notification;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = false;

        // Fix SSL certificate error on local development
        if (app()->environment('local')) {
            MidtransConfig::$curlOptions = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [],
            ];
        }
    }

    /**
     * Create a QRIS / GoPay charge for the given order.
     *
     * @return array{
     *     transaction_id: string,
     *     order_id: string,
     *     gross_amount: string,
     *     payment_type: string,
     *     transaction_status: string,
     *     qr_code_url: string|null,
     *     deeplink_url: string|null,
     *     actions: array,
     *     raw: object
     * }
     */
    public function chargeQris(Order $order): array
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $order->order_ref,
                'gross_amount' => (int) $order->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'item_details' => [
                [
                    'id' => $order->orderable_type.'-'.$order->orderable_id,
                    'price' => (int) $order->unit_price,
                    'quantity' => $order->quantity,
                    'name' => $this->getItemName($order),
                ],
            ],
        ];

        $response = CoreApi::charge($params);

        $qrCodeUrl = null;
        $deeplinkUrl = null;
        $actions = [];

        if (isset($response->actions)) {
            $actions = (array) $response->actions;

            foreach ($response->actions as $action) {
                if ($action->name === 'generate-qr-code') {
                    $qrCodeUrl = $action->url;
                }
                if ($action->name === 'deeplink-redirect') {
                    $deeplinkUrl = $action->url;
                }
            }
        }

        return [
            'transaction_id' => $response->transaction_id,
            'order_id' => $response->order_id,
            'gross_amount' => $response->gross_amount,
            'payment_type' => $response->payment_type,
            'transaction_status' => $response->transaction_status,
            'qr_code_url' => $qrCodeUrl,
            'deeplink_url' => $deeplinkUrl,
            'actions' => $actions,
            'raw' => $response,
        ];
    }

    /**
     * Parse and verify an incoming Midtrans notification.
     *
     * @return array{
     *     order_id: string,
     *     transaction_id: string,
     *     transaction_status: string,
     *     payment_type: string,
     *     gross_amount: string,
     *     signature_key: string,
     *     status_code: string,
     *     fraud_status: string|null
     * }
     */
    public function parseNotification(): array
    {
        $notification = new Notification;

        return [
            'order_id' => $notification->order_id,
            'transaction_id' => $notification->transaction_id,
            'transaction_status' => $notification->transaction_status,
            'payment_type' => $notification->payment_type,
            'gross_amount' => $notification->gross_amount,
            'signature_key' => $notification->signature_key,
            'status_code' => $notification->status_code,
            'fraud_status' => $notification->fraud_status ?? null,
        ];
    }

    /**
     * Verify the signature of a Midtrans notification payload.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Fetch the transaction status from Midtrans API directly.
     */
    public function getTransactionStatus(string $transactionId): object
    {
        return Transaction::status($transactionId);
    }

    /**
     * Derive a human-readable item name from the order's orderable.
     */
    private function getItemName(Order $order): string
    {
        $orderable = $order->orderable;

        if (method_exists($orderable, 'name') || isset($orderable->name)) {
            return $orderable->name;
        }

        return class_basename($order->orderable_type).' #'.$order->orderable_id;
    }
}
