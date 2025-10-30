<?php

namespace App\Exceptions;

/**
 * Exception pour les accès non autorisés
 */
class AccessDeniedException extends ApiException
{
    public function __construct(string $message = 'Accès interdit')
    {
        parent::__construct($message, 403, 'ACCESS_DENIED');
    }
}