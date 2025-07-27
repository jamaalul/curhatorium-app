<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InnerPeaceMembershipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasActiveInnerPeaceMembership()) {
            return redirect()->route('membership.index')
                ->withErrors(['msg' => 'Fitur ini hanya tersedia untuk member Inner Peace. Silakan upgrade membership Anda untuk mengakses laporan mingguan dan bulanan.']);
        }

        return $next($request);
    }
} 