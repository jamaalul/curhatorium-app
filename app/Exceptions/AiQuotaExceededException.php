<?php

namespace App\Exceptions;

use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AiQuotaExceededException extends HttpException
{
    public function __construct(?CarbonInterface $endsAt = null, string $message = 'Kuota token AI kamu sudah habis untuk periode ini.')
    {
        if ($endsAt) {
            $message .= ' Limit akan direset pada '.$endsAt->translatedFormat('j F Y H:i').'.';
        }
        $message .= ' Silakan tunggu atau [upgrade membership]('.url('/membership').').';

        parent::__construct(429, $message);
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 429);
    }
}
