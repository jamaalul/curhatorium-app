<?php

namespace Tests\Unit;

use App\Services\DokuService;
use Tests\TestCase;

class DokuServiceTest extends TestCase
{
    public function test_create_symmetric_signature_follows_doku_snap_formula(): void
    {
        config([
            'doku.secret_key' => 'my_secret_key_123',
        ]);

        $dokuService = new DokuService;

        $httpMethod = 'POST';
        $endpointUrl = '/snap-adapter/b2b/v1.0/qr/qr-mpm-generate';
        $accessToken = 'valid_token_xyz';
        $body = [
            'partnerReferenceNo' => 'REF-001',
            'amount' => ['value' => '10000.00', 'currency' => 'IDR'],
        ];
        $timestamp = '2026-08-19T02:00:00Z';

        $minifiedBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $bodyHash = strtolower(hash('sha256', $minifiedBody));
        $stringToSign = strtoupper($httpMethod).':'.$endpointUrl.':'.$accessToken.':'.$bodyHash.':'.$timestamp;
        $expectedSignature = base64_encode(hash_hmac('sha512', $stringToSign, 'my_secret_key_123', true));

        $actualSignature = $dokuService->createSymmetricSignature(
            $httpMethod,
            $endpointUrl,
            $accessToken,
            $body,
            $timestamp
        );

        $this->assertEquals($expectedSignature, $actualSignature);
        // Base64 encoded SHA-512 must be exactly 88 characters and end with ==
        $this->assertSame(88, strlen($actualSignature));
    }
}
