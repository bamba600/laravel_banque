<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{
    /**
     * Authentifier un utilisateur et retourner les tokens
     */
    public function login(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|string|in:password',
            'client_id' => 'required|integer|exists:oauth_clients,id',
            'client_secret' => 'required|string',
            'username' => 'required|email',
            'password' => 'required|string',
            'scope' => 'nullable|string',
        ]);

        // Vérifier que le client existe et est valide
        $client = Client::where('id', $request->client_id)
                       ->where('secret', $request->client_secret)
                       ->where('password_client', true)
                       ->where('revoked', false)
                       ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'client' => ['Client OAuth invalide ou non autorisé.'],
            ]);
        }

        // Vérifier les credentials utilisateur
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification sont incorrectes.'],
            ]);
        }

        // Générer directement les tokens avec Passport
        try {
            // Créer l'access token (valide 2 heures)
            $accessToken = $user->createToken('API Access Token');
            $accessToken->token->expires_at = now()->addHours(2);
            $accessToken->token->save();

            // Créer le refresh token JWT (valide 1 jour)
            $refreshToken = $user->createToken('API Refresh Token');
            $refreshToken->token->expires_at = now()->addDay();
            $refreshToken->token->save();

            return response()->json([
                'token_type' => 'Bearer',
                'expires_in' => 7200, // 2 heures en secondes
                'access_token' => $accessToken->accessToken,
                'refresh_token' => $refreshToken->accessToken,
                'refresh_token_expires_in' => 86400, // 1 jour en secondes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur lors de l\'authentification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rafraîchir le token d'accès
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|string|in:refresh_token',
            'client_id' => 'required|integer|exists:oauth_clients,id',
            'client_secret' => 'required|string',
            'refresh_token' => 'required|string',
        ]);

        // Vérifier que le client existe et est valide
        $client = Client::where('id', $request->client_id)
                       ->where('secret', $request->client_secret)
                       ->where('password_client', true)
                       ->where('revoked', false)
                       ->first();

        if (!$client) {
            throw ValidationException::withMessages([
                'client' => ['Client OAuth invalide ou non autorisé.'],
            ]);
        }

        // Appeler l'endpoint OAuth de Passport pour rafraîchir le token
        try {
            $response = Http::asForm()->post('http://localhost:8000/oauth/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'refresh_token' => $request->refresh_token,
                'scope' => '*',
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'message' => 'Erreur lors du rafraîchissement du token',
                'error' => $response->json(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur lors du rafraîchissement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Déconnecter l'utilisateur (invalider le token)
     */
    public function logout(Request $request)
    {
        // Révoquer le token d'accès actuel
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        // Révoquer le token d'accès
        $tokenRepository->revokeAccessToken($request->user()->token()->id);

        // Révoquer tous les refresh tokens associés
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($request->user()->token()->id);

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }
}