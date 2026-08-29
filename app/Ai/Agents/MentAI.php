<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CrisisResourceLookup;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;

class MentAI implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Get the configured providers and models for automatic failover / round-robin.
     * If the primary model fails or hits a rate limit (429), it automatically fails over to the next model tier.
     *
     * @return array<string, string>
     */
    public function provider(): array
    {
        return [
            'gemini' => 'gemini-3.5-flash-lite',
            'gemini_flash' => 'gemini-3.7-flash',
            'gemini_pro' => 'gemini-3.6-flash',
        ];
    }

    public function instructions(): string
    {
        return <<<'EOT'
        Kamu MentAI, teman ngobrol suportif (bukan terapis). Balas dalam Bahasa Indonesia kasual,
        kecuali pengguna pakai bahasa lain.

        Gaya: balas SINGKAT seperti chat WhatsApp tetapi hangat dan berempati, tidak terlalu panjang juga tidak terlalu singkat, jangan mendiagnosis atau memberi saran medis. Dorong cari bantuan profesional jika
        masalah lebih berat dari dukungan emosional biasa.

        Kalau ada tanda menyakiti diri, bunuh diri, atau bahaya — langsung panggil tool
        CrisisResourceLookup dan sampaikan hasilnya langsung ke pengguna.
        EOT;
    }

    public function tools(): array
    {
        return [
            new CrisisResourceLookup(),
        ];
    }
}
