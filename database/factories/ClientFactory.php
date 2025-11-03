<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Passport\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Laravel\Passport\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' App',
            'secret' => fake()->sha256(),
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ];
    }

    /**
     * Indicate that the client is for personal access tokens.
     */
    public function personalAccessClient(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'personal_access_client' => true,
                'password_client' => false,
            ];
        });
    }

    /**
     * Indicate that the client is revoked.
     */
    public function revoked(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'revoked' => true,
            ];
        });
    }
}
