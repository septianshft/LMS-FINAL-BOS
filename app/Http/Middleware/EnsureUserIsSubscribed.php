<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSubscribed
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasActiveSubscription()) {
            return redirect()->route('front.pricing')->with('error', 'Anda harus berlangganan untuk mengakses konten ini.');
        }

        return $next($request);
    }
}
