<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API Bancaire Laravel",
 *     version="1.0.0",
 *     description="Documentation complète de l'API bancaire avec gestion des comptes, transactions et blocages automatiques"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Serveur de production"
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur de développement"
 * )
 *
 * @OA\Tag(
 *     name="Comptes",
 *     description="Gestion des comptes bancaires"
 * )
 *
 * @OA\Schema(
 *     schema="Compte",
 *     type="object",
 *     title="Compte bancaire",
 *     description="Objet représentant un compte bancaire",
 *     @OA\Property(property="id", type="string", format="uuid", description="ID unique du compte"),
 *     @OA\Property(property="numero", type="string", description="Numéro du compte (format: CPTXXXXXXXXXXXXX)"),
 *     @OA\Property(property="solde", type="number", format="decimal", description="Solde du compte"),
 *     @OA\Property(property="type", type="string", enum={"epargne", "courant"}, description="Type de compte"),
 *     @OA\Property(property="statut", type="string", enum={"actif", "bloque", "archive"}, description="Statut du compte"),
 *     @OA\Property(property="motifBlocage", type="string", nullable=true, description="Motif du blocage si applicable"),
 *     @OA\Property(property="date_debut_blockage", type="string", format="date-time", nullable=true, description="Date de début du blocage"),
 *     @OA\Property(property="date_fin_blockage", type="string", format="date-time", nullable=true, description="Date de fin du blocage"),
 *     @OA\Property(property="client_id", type="string", format="uuid", description="ID du client propriétaire"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de dernière modification")
 * )
 */
class SwaggerController extends Controller
{
    // Ce contrôleur ne sert qu'aux annotations Swagger
}