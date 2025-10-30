<?php

namespace App\Exceptions;

/**
 * Exception pour les erreurs de validation
 */
class ValidationException extends ApiException
{
    public function __construct(array $errors, string $message = 'Erreurs de validation')
    {
        parent::__construct($message, 422, 'VALIDATION_ERROR', $errors);
    }
}