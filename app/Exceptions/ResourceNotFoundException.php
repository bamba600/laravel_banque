<?php

namespace App\Exceptions;

/**
 * Exception levée quand une ressource n'est pas trouvée
 */
class ResourceNotFoundException extends ApiException
{
    public function __construct(string $resource = 'Ressource', string $identifier = null)
    {
        $message = $identifier
            ? "{$resource} avec l'identifiant '{$identifier}' non trouvée"
            : "{$resource} non trouvée";

        parent::__construct($message, 404, 'RESOURCE_NOT_FOUND');
    }
}