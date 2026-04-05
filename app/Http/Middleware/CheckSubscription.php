<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasActiveSubscription()) {
            return redirect()->route('landing')->with('error', 'Vous devez avoir un abonnement actif pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
