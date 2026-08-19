<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DokuService
{
    private string $clientId;

    private string $secretKey;

    private string $privateKey;

    private string $merchantId;

    private string $terminalId;

    private string $postalCode;

    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = (string) config('doku.client_id', '');
        $this->secretKey = (string) config('doku.secret_key', '');
        $this->privateKey = (string) config('doku.private_key', '');
        $this->merchantId = (string) config('doku.merchant_id', '');
        $this->terminalId = (string) config('doku.terminal_id', 'A01');
        $this->postalCode = (string) config('doku.postal_code', '10110');

        $isProduction = (bool) config('doku.is_production', false);
        $this->baseUrl = $isProduction
            ? (string) config('doku.production_base_url', 'https://api.doku.com')
            : (string) config('doku.sandbox_base_url', 'https://api-sandbox.doku.com');
    }

    /**
     * Get B2B Access Token using Asymmetric Signature (RSA-SHA256).
     *
     * @throws Exception
     */
    public function getB2bToken(): string
    {
        return Cache::remember('doku_b2b_token', 800, function () {
            $timestamp = now()->format('Y-m-d\TH:i:sP');
            $stringToSign = $this->clientId.'|'.$timestamp;

            $signature = $this->createAsymmetricSignature($stringToSign);

            $endpoint = '/authorization/v1/access-token/b2b';

            $headers = [
                'X-CLIENT-KEY' => $this->clientId,
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'Content-Type' => 'application/json',
            ];

            $body = [
                'grantType' => 'client_credentials',
            ];

            $response = Http::withHeaders($headers)->post($this->baseUrl.$endpoint, $body);

            if (! $response->successful() || empty($response->json('accessToken'))) {
                // Mask signature in the error output (avoid leaking full sensitive value in logs/messages)
                $maskedHeaders = $headers;
                $maskedHeaders['X-SIGNATURE'] = substr($signature, 0, 12).'...(masked)';

                $requestForm = [
                    'url' => $this->baseUrl.$endpoint,
                    'headers' => $maskedHeaders,
                    'body' => $body,
                    'string_to_sign' => $stringToSign,
                ];

                Log::error('DOKU getB2bToken error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => $requestForm,
                ]);

                throw new Exception(
                    'Failed to obtain DOKU B2B Access Token. '
                    .'Status: '.$response->status().'. '
                    .'Response: '.$response->body().'. '
                    .'Request: '.json_encode($requestForm)
                );
            }

            return (string) $response->json('accessToken');
        });
    }

    /**
     * Create a QRIS charge for the given order.
     *
     * @return array{
     *     transaction_id: string|null,
     *     order_id: string,
     *     gross_amount: string,
     *     payment_type: string,
     *     transaction_status: string,
     *     qr_code_url: string|null,
     *     deeplink_url: string|null,
     *     actions: array,
     *     raw: array
     * }
     *
     * @throws Exception
     */
    public function chargeQris(Order $order): array
    {
        $token = $this->getB2bToken();
        $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $externalId = (string) (now()->timestamp.random_int(1000, 9999));
        $endpoint = '/snap-adapter/b2b/v1.0/qr/qr-mpm-generate';

        $validityPeriod = $order->expired_at
            ? $order->expired_at->utc()->format('Y-m-d\TH:i:s\Z')
            : now()->addMinutes(15)->utc()->format('Y-m-d\TH:i:s\Z');

        $body = [
            'partnerReferenceNo' => $order->order_ref,
            'amount' => [
                'value' => number_format((float) $order->gross_amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'merchantId' => $this->merchantId,
            'terminalId' => $this->terminalId,
            'validityPeriod' => $validityPeriod,
            'additionalInfo' => [
                'postalCode' => $this->postalCode,
                'feeType' => '1',
            ],
        ];

        $signature = $this->createSymmetricSignature('POST', $endpoint, $token, $body, $timestamp);

        $response = Http::withHeaders([
            'X-PARTNER-ID' => $this->clientId,
            'X-EXTERNAL-ID' => $externalId,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'Authorization' => 'Bearer '.$token,
            'CHANNEL-ID' => 'H2H',
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.$endpoint, $body);

        $responseData = $response->json() ?? [];

        if (! $response->successful() || empty($responseData['qrContent'])) {
            Log::error('DOKU chargeQris failed', [
                'order_ref' => $order->order_ref,
                'status' => $response->status(),
                'response' => $responseData,
            ]);
            throw new Exception('Failed to generate DOKU QRIS: '.($responseData['responseMessage'] ?? $response->body()));
        }

        $referenceNo = $responseData['referenceNo'] ?? null;
        $qrContent = $responseData['qrContent'] ?? null;

        return [
            'transaction_id' => $referenceNo,
            'order_id' => $order->order_ref,
            'gross_amount' => (string) $order->gross_amount,
            'payment_type' => 'qris',
            'transaction_status' => 'pending',
            'qr_code_url' => $qrContent,
            'deeplink_url' => null,
            'actions' => [],
            'raw' => $responseData,
        ];
    }

    /**
     * Query QRIS transaction status.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function queryQris(string $referenceNo, string $partnerReferenceNo): array
    {
        $token = $this->getB2bToken();
        $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $externalId = (string) (now()->timestamp.random_int(1000, 9999));
        $endpoint = '/snap-adapter/b2b/v1.0/qr/qr-mpm-query';

        $body = [
            'originalReferenceNo' => $referenceNo,
            'originalPartnerReferenceNo' => $partnerReferenceNo,
            'serviceCode' => '47',
            'merchantId' => $this->merchantId,
        ];

        $signature = $this->createSymmetricSignature('POST', $endpoint, $token, $body, $timestamp);

        $response = Http::withHeaders([
            'X-PARTNER-ID' => $this->clientId,
            'X-EXTERNAL-ID' => $externalId,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'Authorization' => 'Bearer '.$token,
            'CHANNEL-ID' => 'H2H',
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.$endpoint, $body);

        $responseData = $response->json() ?? [];

        if (! $response->successful()) {
            Log::warning('DOKU queryQris failed', [
                'partnerReferenceNo' => $partnerReferenceNo,
                'status' => $response->status(),
                'response' => $responseData,
            ]);
            throw new Exception('Failed to query DOKU QRIS status: '.($responseData['responseMessage'] ?? $response->body()));
        }

        return $responseData;
    }

    /**
     * Cancel / Expire QRIS transaction.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function cancelQris(string $referenceNo, string $partnerReferenceNo, string $reason = 'Customer Cancelled'): array
    {
        $token = $this->getB2bToken();
        $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $externalId = (string) (now()->timestamp.random_int(1000, 9999));
        $endpoint = '/snap-adapter/b2b/v1.0/qr/qr-expire';

        $body = [
            'partnerReferenceNo' => $partnerReferenceNo,
            'referenceNo' => $referenceNo,
            'merchantId' => $this->merchantId,
            'reason' => $reason,
        ];

        $signature = $this->createSymmetricSignature('POST', $endpoint, $token, $body, $timestamp);

        $response = Http::withHeaders([
            'X-PARTNER-ID' => $this->clientId,
            'X-EXTERNAL-ID' => $externalId,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'Authorization' => 'Bearer '.$token,
            'CHANNEL-ID' => 'H2H',
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.$endpoint, $body);

        return $response->json() ?? [];
    }

    /**
     * Generate RSA-SHA256 Asymmetric Signature for B2B Token Request.
     *
     * @throws Exception
     */
    public function createAsymmetricSignature(string $stringToSign): string
    {
        $formattedKey = $this->formatPrivateKey($this->privateKey);
        $privateKeyResource = openssl_pkey_get_private($formattedKey);

        if (! $privateKeyResource) {
            throw new Exception('Invalid DOKU private key configuration: '.openssl_error_string());
        }

        $binarySignature = '';
        $success = openssl_sign($stringToSign, $binarySignature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        if (! $success) {
            throw new Exception('Failed to create RSA-SHA256 signature: '.openssl_error_string());
        }

        return base64_encode($binarySignature);
    }

    /**
     * Generate HMAC-SHA512 Symmetric Signature for SNAP Services.
     */
    public function createSymmetricSignature(
        string $httpMethod,
        string $endpointUrl,
        string $accessToken,
        array $body,
        string $timestamp
    ): string {
        $minifiedBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $bodyHash = strtolower(hash('sha256', $minifiedBody !== false ? $minifiedBody : ''));

        $stringToSign = strtoupper($httpMethod).':'.$endpointUrl.':'.$accessToken.':'.$bodyHash.':'.$timestamp;

        return hash_hmac('sha512', $stringToSign, $this->secretKey);
    }

    /**
     * Format private key string to standard PEM format if not already formatted.
     */
    private function formatPrivateKey(string $key): string
    {
        $key = str_replace(["\r\n", "\r"], "\n", trim($key));

        if (str_contains($key, '-----BEGIN')) {
            return str_replace('\\n', "\n", $key);
        }

        return "-----BEGIN PRIVATE KEY-----\n".wordwrap($key, 64, "\n", true)."\n-----END PRIVATE KEY-----";
    }
}
