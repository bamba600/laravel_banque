<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\ApiResponseTrait;

/**
 * Exception de base pour l'API
 * Toutes les exceptions API héritent de cette classe
 */
class ApiException extends Exception
{
    use ApiResponseTrait;

    protected $statusCode;
    protected $errorCode;
    protected $errors;

    public function __construct(
        string $message = 'Erreur API',
        int $statusCode = 400,
        string $errorCode = 'API_ERROR',
        array $errors = [],
        \Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);

        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->errors = $errors;
    }

    /**
     * Retourne la réponse JSON formatée
     */
    public function render(): JsonResponse
    {
        return $this->errorResponse(
            $this->message,
            $this->statusCode,
            $this->errors,
            $this->errorCode
        );
    }

    // Getters
    public function getStatusCode(): int { return $this->statusCode; }
    public function getErrorCode(): string { return $this->errorCode; }
    public function getErrors(): array { return $this->errors; }
}