<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateProfessional
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('professional')->check()) {
            return redirect()->route('professional.login');
        }

        // Get the professional ID from the route
        $professionalId = $request->route('professionalId');
        
        // Ensure the authenticated professional can only access their own dashboard
        if ($professionalId && Auth::guard('professional')->id() != $professionalId) {
            abort(403, 'Unauthorized access to professional dashboard.');
        }

        return $next($request);
    }
}
