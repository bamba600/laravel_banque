<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponseTrait
 *
 * Fournit des méthodes standardisées pour formater les réponses API
 * Utilise un format JSON cohérent pour toutes les réponses
 *
 * @package App\Http\Traits
 */
trait ApiResponseTrait
{
    /**
     * Format de réponse standard pour les succès
     *
     * @param mixed $data Données à retourner
     * @param array $meta Métadonnées supplémentaires (pagination, links, etc.)
     * @param int $statusCode Code HTTP (défaut: 200)
     * @return JsonResponse
     */
    protected function successResponse($data = null, array $meta = [], int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ];

        // Ajouter les métadonnées si elles existent
        if (!empty($meta)) {
            $response = array_merge($response, $meta);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Format de réponse standard pour les erreurs
     *
     * @param string $message Message d'erreur
     * @param int $statusCode Code HTTP d'erreur
     * @param array $errors Détails des erreurs de validation (optionnel)
     * @param string|null $errorCode Code d'erreur interne (optionnel)
     * @return JsonResponse
     */
    protected function errorResponse(
        string $message,
        int $statusCode = 400,
        array $errors = [],
        ?string $errorCode = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ];

        // Ajouter les détails d'erreur si présents
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        // Ajouter le code d'erreur si présent
        if ($errorCode) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Réponse de succès pour les opérations de création
     *
     * @param mixed $data Données de la ressource créée
     * @param string $message Message de succès personnalisé
     * @return JsonResponse
     */
    protected function createdResponse($data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return $this->successResponse($data, ['message' => $message], 201);
    }

    /**
     * Réponse de succès pour les opérations de mise à jour
     *
     * @param mixed $data Données de la ressource mise à jour
     * @param string $message Message de succès personnalisé
     * @return JsonResponse
     */
    protected function updatedResponse($data = null, string $message = 'Ressource mise à jour avec succès'): JsonResponse
    {
        return $this->successResponse($data, ['message' => $message], 200);
    }

    /**
     * Réponse de succès pour les opérations de suppression
     *
     * @param string $message Message de succès personnalisé
     * @return JsonResponse
     */
    protected function deletedResponse(string $message = 'Ressource supprimée avec succès'): JsonResponse
    {
        return $this->successResponse(null, ['message' => $message], 200);
    }

    /**
     * Réponse pour les erreurs de validation
     *
     * @param array $errors Erreurs de validation
     * @param string $message Message d'erreur général
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Erreurs de validation'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors, 'VALIDATION_ERROR');
    }

    /**
     * Réponse pour les erreurs d'authentification
     *
     * @param string $message Message d'erreur
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Non autorisé'): JsonResponse
    {
        return $this->errorResponse($message, 401, [], 'UNAUTHORIZED');
    }

    /**
     * Réponse pour les erreurs d'accès interdit
     *
     * @param string $message Message d'erreur
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Accès interdit'): JsonResponse
    {
        return $this->errorResponse($message, 403, [], 'FORBIDDEN');
    }

    /**
     * Réponse pour les ressources non trouvées
     *
     * @param string $message Message d'erreur
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return $this->errorResponse($message, 404, [], 'NOT_FOUND');
    }

    /**
     * Réponse pour les erreurs internes du serveur
     *
     * @param string $message Message d'erreur
     * @param string|null $errorCode Code d'erreur interne
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Erreur interne du serveur', ?string $errorCode = null): JsonResponse
    {
        return $this->errorResponse($message, 500, [], $errorCode ?? 'INTERNAL_ERROR');
    }

    /**
     * Réponse pour les erreurs de limite de taux (rate limiting)
     *
     * @param string $message Message d'erreur
     * @param int $retryAfter Secondes avant nouvelle tentative
     * @return JsonResponse
     */
    protected function rateLimitResponse(string $message = 'Trop de requêtes', int $retryAfter = 60): JsonResponse
    {
        return $this->errorResponse($message, 429, [
            'retry_after' => $retryAfter,
            'retry_after_human' => now()->addSeconds($retryAfter)->diffForHumans()
        ], 'RATE_LIMIT_EXCEEDED');
    }

    /**
     * Réponse paginée standardisée
     *
     * @param mixed $data Données paginées
     * @param array $pagination Métadonnées de pagination
     * @param array $links Liens de navigation
     * @return JsonResponse
     */
    protected function paginatedResponse($data, array $pagination, array $links = []): JsonResponse
    {
        $meta = [
            'pagination' => $pagination
        ];

        if (!empty($links)) {
            $meta['links'] = $links;
        }

        return $this->successResponse($data, $meta);
    }
}