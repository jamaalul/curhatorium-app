<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AiQuotaExceededException extends HttpException
{
    public function __construct(string $message = 'Kuota token AI kamu sudah habis untuk periode ini.')
    {
        parent::__construct(429, $message);
    }
}
