<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Gère les autorisations basées sur les rôles des utilisateurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        $user = $request->auth_user;

        // Vérifie le rôle si spécifié
        if ($role) {
            $allowedTypes = explode('|', $role);
            if (!in_array($user->type, $allowedTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Accès non autorisé pour votre type de compte.',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        // Gestion des autorisations spécifiques pour les clients
        if ($user->type === 'client') {
            // Vérifie l'accès aux comptes
            $compteId = $request->route('compteId');
            if ($compteId) {
                $compte = \App\Models\Compte::find($compteId);
                if (!$compte || $compte->client_id !== $user->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Vous n\'êtes pas autorisé à accéder à ce compte.',
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            // Filtre automatique pour la liste des comptes
            if ($request->is('api/v1/comptes')) {
                $request->merge(['client_id' => $user->id]);
            }
        }

        // Les administrateurs ont accès à tout
        if ($user->type === 'admin') {
            // On pourrait ajouter des logs ou des vérifications supplémentaires ici
            $request->merge(['is_admin' => true]);
        }

        return $next($request);
    }
}