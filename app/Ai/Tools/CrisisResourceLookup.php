<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CrisisResourceLookup implements Tool
{
    public function description(): Stringable|string
    {
        return 'Retrieve crisis hotline and emergency resources. Call this whenever the user expresses thoughts of self-harm, suicide, or being in immediate danger.';
    }

    public function handle(Request $request): Stringable|string
    {
        return <<<'EOT'
        Kamu tidak sendirian, dan ada orang yang bisa membantu sekarang:

        - Kementerian Kesehatan RI - Layanan Sejiwa: 119 ext. 8 (gratis, 24 jam)
        - Into The Light Indonesia: intothelightid.org
        - LISA Suicide Prevention Helpline: 0811-3855-472 (chat/WhatsApp)
        - Dalam bahaya langsung: hubungi 112 atau ke IGD rumah sakit terdekat.
        EOT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
