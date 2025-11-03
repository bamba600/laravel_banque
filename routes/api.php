<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CompteController;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| API V1 Routes - Version 1 de l'API
|--------------------------------------------------------------------------
|
| Routes pour la première version de l'API bancaire
| Préfixe: /api/v1/
|
*/
Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Routes d'authentification
    |--------------------------------------------------------------------------
    |
    | Authentification OAuth avec Passport
    |
    */
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::middleware('auth.api')->post('/auth/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Routes des Comptes
    |--------------------------------------------------------------------------
    |
    | Gestion des comptes bancaires
    | Middlewares appliqués:
    | - auth.api : Vérifie l'authentification
    | - role:admin : Vérifie si l'utilisateur est administrateur
    | - compte.owner : Vérifie si l'utilisateur est propriétaire du compte
    |
    */

    /**
     * Lister tous les comptes non archivés (Admin seulement)
     *
     * GET /api/v1/comptes?page=1&limit=10&type=epargne&statut=actif&sort=dateCreation&order=desc
     *
     * Middlewares: auth.api, role:admin
     */
    Route::get('/comptes', [CompteController::class, 'index'])
        ->middleware(['auth.api', 'role:admin'])
        ->name('api.v1.comptes.index');

    /**
     * Bloquer un compte (Admin seulement)
     *
     * POST /api/v1/comptes/{compteId}/bloquer
     *
     * Middlewares: auth.api, role:admin
     */
    Route::post('/comptes/{compteId}/bloquer', [CompteController::class, 'bloquer'])
        ->middleware(['auth.api', 'role:admin'])
        ->name('api.v1.comptes.bloquer')
        ->where('compteId', '[a-f0-9\-]+');

    /**
     * Récupérer un compte par son numéro
     *
     * GET /api/v1/comptes/{numero}
     *
     * Middlewares: 
     * - auth.api : Vérifie l'authentification
     * - role:admin|compte.owner : Accès autorisé si admin OU propriétaire du compte
     */
    Route::get('/comptes/{numero}', [CompteController::class, 'show'])
        ->middleware(['auth.api', 'role:admin|client'])
            ->name('api.v1.comptes.show')
        ->where('numero', '[A-Z0-9]+');

    /**
     * Récupérer les comptes d'un client par téléphone
     *
     * GET /api/v1/comptes/client/{telephone}
     *
     * Middlewares: 
     * - auth.api : Vérifie l'authentification
     * - role:admin|compte.owner : Accès autorisé si admin OU propriétaire du compte
     */
    Route::get('/comptes/client/{telephone}', [CompteController::class, 'getComptesByClient'])
        ->middleware(['auth.api', 'role:admin|client'])
            ->name('api.v1.comptes.client')
        ->where('telephone', '[0-9+\-\s]+');
});

/*
|--------------------------------------------------------------------------
| Routes pour les informations utilisateur
|--------------------------------------------------------------------------
|
| Routes pour récupérer les informations de l'utilisateur connecté
|
*/
Route::middleware('auth.api')->get('/user', function (Request $request) {
    return $request->user();
});
