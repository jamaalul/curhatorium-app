<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CrisisResourceLookup;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Gemini)]
#[Model('gemini-3.1-flash-lite')]
class MentAI implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

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

    public function tools(): iterable
    {
        return [
            new CrisisResourceLookup,
        ];
    }
}