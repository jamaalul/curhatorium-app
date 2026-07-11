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
        if (! $this->token) {
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

    /**
     * Send a WhatsApp message scheduled for a future Unix timestamp.
     * Fonnte delivers the message at the given time — no server-side scheduler needed.
     *
     * @param  string  $phoneNumber  Target phone number
     * @param  string  $message  Message body
     * @param  int  $scheduleTimestamp  Unix timestamp (seconds) when the message should be delivered
     */
    public function sendScheduledWhatsApp(string $phoneNumber, string $message, int $scheduleTimestamp): bool
    {
        if (! $this->token) {
            Log::error('Fonnte API key is not set.');

            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $phoneNumber,
            'message' => $message,
            'schedule' => $scheduleTimestamp,
        ]);

        if ($response->failed()) {
            Log::error('Failed to send scheduled WhatsApp message', [
                'response' => $response->body(),
                'phone_number' => $phoneNumber,
                'schedule' => $scheduleTimestamp,
            ]);

            return false;
        }

        Log::info('Scheduled WhatsApp message queued via Fonnte', [
            'phone_number' => $phoneNumber,
            'schedule' => $scheduleTimestamp,
        ]);

        return true;
    }
}
