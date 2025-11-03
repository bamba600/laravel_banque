<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\PassportTestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase, PassportTestCase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurer Passport pour les tests
        $this->setUpPassport();
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        // Créer un utilisateur de test
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'type' => User::TYPE_CLIENT,
        ]);

        // Tenter de se connecter directement via la route Passport
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->oauthClient->id,
            'client_secret' => $this->oauthClient->secret,
            'username' => 'test@example.com',
            'password' => 'password123',
            'scope' => '*',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token_type',
                    'expires_in',
                    'access_token',
                    'refresh_token',
                ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        // Tenter de se connecter avec des identifiants invalides
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->oauthClient->id,
            'client_secret' => $this->oauthClient->secret,
            'username' => 'invalid@example.com',
            'password' => 'wrongpassword',
            'scope' => '*',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_can_refresh_token()
    {
        // D'abord se connecter pour obtenir un refresh token
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'type' => User::TYPE_CLIENT,
        ]);

        $loginResponse = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->oauthClient->id,
            'client_secret' => $this->oauthClient->secret,
            'username' => $user->email,
            'password' => 'password123',
            'scope' => '*',
        ]);

        $refreshToken = $loginResponse->json('refresh_token');

        // Utiliser le refresh token
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->oauthClient->id,
            'client_secret' => $this->oauthClient->secret,
            'refresh_token' => $refreshToken,
            'scope' => '*',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token_type',
                    'expires_in',
                    'access_token',
                    'refresh_token',
                ]);
    }
}