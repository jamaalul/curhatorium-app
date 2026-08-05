<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AiSubscriptionRequiredException extends HttpException
{
    public function __construct(string $message = 'Kamu membutuhkan langganan aktif untuk menggunakan fitur AI.')
    {
        parent::__construct(403, $message);
    }
}
