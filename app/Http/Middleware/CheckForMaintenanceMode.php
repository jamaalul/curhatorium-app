<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckForMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('maintenance.enabled')) {
            if (config('maintenance.scheduled_at') && now()->lt(config('maintenance.scheduled_at'))) {
                return $next($request);
            }

            $allowedIps = explode(',', config('maintenance.allow'));
            if (!in_array($request->ip(), $allowedIps)) {
                Log::info('Maintenance mode activated for IP: ' . $request->ip());
                if ($request->expectsJson()) {
                    return response()->json(['message' => config('maintenance.message')], 503);
                }
                return response()->view('maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
