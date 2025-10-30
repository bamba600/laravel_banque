<?php

/**
 * @OA\Info(
 *     title="API Bancaire Laravel",
 *     version="1.0.0",
 *     description="Documentation complète de l'API bancaire avec gestion des comptes, transactions et blocages automatiques"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Serveur de développement"
 * )
 *
 * @OA\Tag(
 *     name="Comptes",
 *     description="Gestion des comptes bancaires"
 * )
 */

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Exceptions\ResourceNotFoundException;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompteCollection;
use App\Http\Resources\CompteResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Compte;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompteController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/v1/comptes",
     *     summary="Lister les comptes bancaires",
     *     description="Récupère la liste paginée des comptes non archivés avec possibilité de filtrage par type, statut et tri",
     *     operationId="listerComptes",
     *     tags={"Comptes"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page (défaut: 1)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page (défaut: 10, max: 100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrer par type de compte",
     *         required=false,
     *         @OA\Schema(type="string", enum={"epargne", "courant"}, example="epargne")
     *     ),
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Filtrer par statut du compte",
     *         required=false,
     *         @OA\Schema(type="string", enum={"actif", "bloque"}, example="actif")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Champ de tri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"dateCreation", "numero", "solde"}, example="dateCreation")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Ordre de tri",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Compte")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Validation des paramètres
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|string|in:epargne,courant',
            'statut' => 'nullable|string|in:actif,bloque',
            'sort' => 'nullable|string|in:dateCreation,numero,solde',
            'order' => 'nullable|string|in:asc,desc'
        ]);

        // Construction de la requête avec les scopes
        $query = Compte::with('client:id,name,prenom')
                      ->nonArchive(); // Scope global pour exclure les comptes archivés

        // Application des filtres
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        // Mapping des champs de tri
        $sortMapping = [
            'dateCreation' => 'created_at',
            'numero' => 'numero',
            'solde' => 'solde'
        ];

        $actualSort = $sortMapping[$sort] ?? $sort;
        $query->orderBy($actualSort, $order);

        // Pagination
        $perPage = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $comptes = $query->paginate($perPage, ['*'], 'page', $page);

        // Utilisation des API Resources pour la transformation
        return new CompteCollection($comptes);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/comptes/{numero}",
     *     summary="Détail d'un compte bancaire",
     *     description="Récupère les informations détaillées d'un compte par son numéro unique",
     *     operationId="detailCompte",
     *     tags={"Comptes"},
     *     @OA\Parameter(
     *         name="numero",
     *         in="path",
     *         description="Numéro du compte (format: CPT suivi de 13 chiffres)",
     *         required=true,
     *         @OA\Schema(type="string", pattern="^CPT[0-9]{13}$", example="CPT0000000000001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du compte récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Compte")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ressource non trouvée")
     *         )
     *     )
     * )
     */
    public function show(string $numero)
    {
        $compte = Compte::numero($numero)->first();

        if (!$compte) {
            throw new ResourceNotFoundException('Compte', $numero);
        }

        return $this->successResponse(new CompteResource($compte));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/comptes/client/{telephone}",
     *     summary="Comptes d'un client",
     *     description="Récupère tous les comptes d'un client via son numéro de téléphone",
     *     operationId="comptesParClient",
     *     tags={"Comptes"},
     *     @OA\Parameter(
     *         name="telephone",
     *         in="path",
     *         description="Numéro de téléphone du client",
     *         required=true,
     *         @OA\Schema(type="string", example="+221771234567")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comptes du client récupérés",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Compte")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé ou aucun compte"
     *     )
     * )
     */
    public function getComptesByClient(string $telephone, Request $request)
    {
        // Normaliser le numéro de téléphone pour la recherche (supprimer les espaces)
        $normalizedTelephone = preg_replace('/\s+/', '', $telephone);

        $query = Compte::with('client:id,name,prenom')
                      ->whereHas('client', function ($clientQuery) use ($normalizedTelephone) {
                          // Comparer avec le numéro normalisé dans la base de données
                          $clientQuery->whereRaw("REPLACE(telephone, ' ', '') = ?", [$normalizedTelephone]);
                      });

        // Pagination d'abord
        $perPage = $request->get('limit', 10);
        $comptes = $query->paginate($perPage);

        // Vérifier si des comptes ont été trouvés après pagination
        if ($comptes->total() === 0) {
            throw new ResourceNotFoundException('Client', $telephone);
        }

        return $this->paginatedResponse(
            CompteResource::collection($comptes),
            [
                'currentPage' => $comptes->currentPage(),
                'totalPages' => $comptes->lastPage(),
                'totalItems' => $comptes->total(),
                'itemsPerPage' => $comptes->perPage(),
                'hasNext' => $comptes->hasMorePages(),
                'hasPrevious' => $comptes->currentPage() > 1
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/comptes/{compteId}/bloquer",
     *     summary="Bloquer un compte bancaire",
     *     description="Permet de bloquer un compte épargne avec des dates de début et fin optionnelles. Seuls les comptes épargne peuvent être bloqués.",
     *     operationId="bloquerCompte",
     *     tags={"Comptes"},
     *     @OA\Parameter(
     *         name="compteId",
     *         in="path",
     *         description="ID du compte à bloquer",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Informations de blocage du compte",
     *         @OA\JsonContent(
     *             required={"motif"},
     *             @OA\Property(property="motif", type="string", maxLength=255, example="Suspicion de fraude", description="Motif du blocage"),
     *             @OA\Property(property="date_debut_blockage", type="string", format="date-time", example="2025-10-30 14:00:00", description="Date de début du blocage (optionnel, défaut = maintenant)"),
     *             @OA\Property(property="date_fin_blockage", type="string", format="date-time", example="2025-11-30 14:00:00", description="Date de fin du blocage (optionnel)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte bloqué avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Compte bloqué avec succès")
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="version", type="string", example="1.0.0")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation ou compte non éligible au blocage",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Seuls les comptes épargne peuvent être bloqués."),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="version", type="string", example="1.0.0")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ressource non trouvée"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="version", type="string", example="1.0.0")
     *         )
     *     )
     * )
     */
    public function bloquer(string $compteId, Request $request)
    {
        $validated = $request->validate([
            'motif' => 'required|string|max:255',
            'date_debut_blockage' => 'nullable|date|after:now',
            'date_fin_blockage' => 'nullable|date|after:date_debut_blockage',
        ]);

        $compte = Compte::findOrFail($compteId);

        // Validation: seuls les comptes épargne peuvent être bloqués
        if ($compte->type !== 'epargne') {
            throw new ApiException('Seuls les comptes épargne peuvent être bloqués.', 400);
        }

        $dateDebut = isset($validated['date_debut_blockage'])
            ? Carbon::parse($validated['date_debut_blockage'])
            : now();

        $dateFin = isset($validated['date_fin_blockage'])
            ? Carbon::parse($validated['date_fin_blockage'])
            : null;

        $compte->update([
            'statut' => 'bloque',
            'motifBlocage' => $validated['motif'],
            'date_debut_blockage' => $dateDebut,
            'date_fin_blockage' => $dateFin,
        ]);

        return $this->successResponse(
            ['message' => 'Compte bloqué avec succès']
        );
    }
}