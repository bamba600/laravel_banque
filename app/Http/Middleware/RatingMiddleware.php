<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RatingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $key = $user ? 'user:' . $user->id : 'ip:' . $request->ip();
        $maxAttempts = 2; // Configurable: max requêtes par fenêtre
        $decayMinutes = 1; // Configurable: fenêtre de temps en minutes

        // Vérifier si la limite de taux est dépassée
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            // Enregistrer l'utilisateur/IP qui a atteint la limite
            Log::warning('Limite de Notation Atteinte', [
                'user_id' => $user ? $user->id : null,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'timestamp' => now(),
            ]);
        } else {
            // Incrémenter le compteur de tentatives
            RateLimiter::hit($key, $decayMinutes * 60); // Décroissance en secondes
        }

        return $next($request);
    }
}