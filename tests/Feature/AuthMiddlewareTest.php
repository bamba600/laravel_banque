<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_blocks_unauthenticated_access()
    {
        $response = $this->getJson('/api/v1/test-auth');

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Non autorisé. Veuillez vous connecter.'
                ]);
    }

    /** @test */
    public function it_blocks_inactive_user_access()
    {
        // Créer un utilisateur inactif
        $user = User::factory()->create([
            'statut' => 'inactif',
            'type' => User::TYPE_CLIENT
        ]);

        // Créer un token directement
        $token = $user->createToken('Test Token')->accessToken;

        // Tester l'accès avec le token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/v1/test-auth');

        $response->assertStatus(403)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Votre compte est inactif. Veuillez contacter l\'administrateur.'
                ]);
    }

    /** @test */
    public function it_allows_active_user_access()
    {
        // Créer un utilisateur actif
        $user = User::factory()->create([
            'statut' => 'actif',
            'type' => User::TYPE_CLIENT
        ]);

        // Créer un token directement
        $token = $user->createToken('Test Token')->accessToken;

        // Tester l'accès avec le token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/v1/test-auth');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Accès autorisé'
                ]);
    }
}