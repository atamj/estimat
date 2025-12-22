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

        if (!$user) {
            return redirect()->route('login');
        }

        $subscription = $user->activeSubscription;

        if (!$subscription || ($subscription->ends_at && $subscription->ends_at->isPast())) {
            // Si pas d'abonnement actif, on pourrait rediriger vers la page de choix de plan
            // Pour l'instant on laisse passer si c'est un utilisateur de test ou on redirige vers landing
            return redirect()->route('landing')->with('error', 'Vous devez avoir un abonnement actif pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
