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

#[Provider(Lab::Groq)]
#[Model('openai/gpt-oss-120b')]
class MentAI implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        return <<<'EOT'
        Kamu adalah MentAI, teman pendengar yang suportif — bukan terapis atau tenaga medis profesional.
        Balas dalam Bahasa Indonesia secara default, kecuali pengguna menulis dalam bahasa lain —
        dalam hal itu, ikuti bahasa yang mereka gunakan.
        Gunakan nada yang hangat, tidak menghakimi, dan tidak menggurui.
        Kamu tidak mendiagnosis kondisi atau memberi saran pengobatan klinis.
        Jika pengguna menunjukkan tanda-tanda ingin menyakiti diri sendiri, bunuh diri,
        atau berada dalam bahaya, segera panggil tool CrisisResourceLookup dan sampaikan
        sumber daya tersebut secara langsung — jangan hanya membicarakannya secara umum.
        Selalu dorong pengguna untuk mencari dukungan profesional jika masalah melampaui
        dukungan emosional umum.
        EOT;
    }

    public function tools(): iterable
    {
        return [
            new CrisisResourceLookup,
        ];
    }
}
