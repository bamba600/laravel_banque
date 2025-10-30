<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CompteController;

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
    | Routes des Comptes
    |--------------------------------------------------------------------------
    |
    | Gestion des comptes bancaires
    | Middleware: api (fourni par Laravel)
    |
    */

    /**
     * Lister tous les comptes non archivés
     *
     * GET /api/v1/comptes?page=1&limit=10&type=epargne&statut=actif&sort=dateCreation&order=desc
     *
     * Paramètres de requête:
     * - page: numéro de page (défaut: 1)
     * - limit: nombre d'éléments par page (défaut: 10, max: 100)
     * - type: filtre par type de compte (epargne, courant)
     * - statut: filtre par statut (actif, bloque)
     * - sort: champ de tri (dateCreation, numero, solde)
     * - order: ordre de tri (asc, desc)
     *
     * Réponse: Liste paginée des comptes avec métadonnées
     */
    Route::get('/comptes', [CompteController::class, 'index'])
        ->name('api.v1.comptes.index');

    /**
     * Récupérer un compte par son numéro
     *
     * GET /api/v1/comptes/{numero}
     *
     * Paramètres:
     * - numero: numéro du compte (string)
     *
     * Réponse: Détails du compte ou 404 si non trouvé
     */
    Route::get('/comptes/{numero}', [CompteController::class, 'show'])
        ->name('api.v1.comptes.show')
        ->where('numero', '[A-Z0-9]+'); // Contrainte sur le format du numéro

    /**
     * Récupérer les comptes d'un client par téléphone
     *
     * GET /api/v1/comptes/client/{telephone}?page=1&limit=10
     *
     * Paramètres:
     * - telephone: numéro de téléphone du client
     * - page: numéro de page (optionnel)
     * - limit: nombre d'éléments par page (optionnel)
     *
     * Réponse: Liste paginée des comptes du client
     */
    Route::get('/comptes/client/{telephone}', [CompteController::class, 'getComptesByClient'])
        ->name('api.v1.comptes.client')
        ->where('telephone', '[0-9+\-\s]+'); // Contrainte sur le format téléphone

    /**
     * Bloquer un compte
     *
     * POST /api/v1/comptes/{compteId}/bloquer
     */
    Route::post('/comptes/{compteId}/bloquer', [CompteController::class, 'bloquer'])
        ->name('api.v1.comptes.bloquer')
        ->where('compteId', '[a-f0-9\-]+'); // UUID constraint

});

/*
|--------------------------------------------------------------------------
| Routes d'authentification existantes
|--------------------------------------------------------------------------
|
| Routes existantes conservées pour compatibilité
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
