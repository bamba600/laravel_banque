<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Vérifie si l'utilisateur est authentifié.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Vérifie si un token est présent
            if (!$request->bearerToken()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token d\'authentification manquant',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Vérifie si l'utilisateur est authentifié
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token d\'authentification invalide',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Vérifie si le compte est actif
            $user = Auth::guard('api')->user();
            if ($user->statut === 'inactif') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Votre compte est inactif. Veuillez contacter l\'administrateur.',
                ], Response::HTTP_FORBIDDEN);
            }

            // Ajoute l'utilisateur à la requête pour les autres middlewares
            $request->merge(['auth_user' => $user]);

            return $next($request);

        } catch (\Exception $e) {
            // Log l'erreur en développement
            if (app()->environment('local')) {
                Log::error('Erreur d\'authentification: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'authentification',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}