<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProfileUploadRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            throw ValidationException::withMessages([
                'profile_picture' => 'Too many upload attempts. Please try again in ' . 
                    ceil(RateLimiter::availableIn($key) / 60) . ' minutes.',
            ]);
        }

        RateLimiter::hit($key, $this->decayMinutes() * 60);

        $response = $next($request);

        return $response->header('X-RateLimit-Limit', $this->maxAttempts())
                       ->header('X-RateLimit-Remaining', RateLimiter::remaining($key, $this->maxAttempts()));
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(implode('|', [
            $request->user()->id ?? $request->ip(),
            $request->userAgent(),
            'profile_upload'
        ]));
    }

    /**
     * Get the maximum number of attempts for the rate limiter.
     */
    protected function maxAttempts(): int
    {
        return 5; // 5 uploads per time window
    }

    /**
     * Get the number of minutes to decay the rate limiter.
     */
    protected function decayMinutes(): int
    {
        return 60; // 1 hour window
    }
} 