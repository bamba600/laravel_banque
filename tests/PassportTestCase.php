<?php

namespace Tests;

use Laravel\Passport\Passport;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

trait PassportTestCase
{
    protected Client $oauthClient;

    protected function setUpPassport(): void
    {
        // Créer les clés Passport manuellement pour les tests
        $this->artisan('passport:keys', ['--force' => true]);

        // Créer un client OAuth password grant via le repository
        $clientRepository = new ClientRepository();
        $this->oauthClient = $clientRepository->createPasswordGrantClient(
            null,
            'Test Password Grant Client',
            'http://localhost'
        );
    }

    protected function getOAuthClient(): Client
    {
        return $this->oauthClient;
    }
}