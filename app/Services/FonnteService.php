<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    public function sendWhatsApp($phoneNumber, $message)
    {
        if (!$this->token) {
            Log::error('Fonnte API key is not set.');
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $phoneNumber,
            'message' => $message,
        ]);

        if ($response->failed()) {
            Log::error('Failed to send WhatsApp message', [
                'response' => $response->body(),
                'phone_number' => $phoneNumber,
            ]);
            return false;
        }

        return $response->json();
    }
}