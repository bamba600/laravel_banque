<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Champs de base communs
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Passer@123'),
            'remember_token' => Str::random(10),
            'type' => User::TYPE_CLIENT, // Par défaut, on crée un client

            // Champs spécifiques client
            'prenom' => fake()->firstName(),
            'telephone' => '+221 7' . random_int(0, 9) . ' ' . 
                          str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT) . ' ' .
                          str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT) . ' ' .
                          str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT),
            'adresse' => fake()->address(),
            'statut' => User::STATUT_ACTIF
        ];
    }

    /**
     * État pour créer un administrateur
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::TYPE_ADMIN,
                // Mettre à null les champs spécifiques client
                'prenom' => null,
                'telephone' => null,
                'adresse' => null,
                'statut' => null
            ];
        });
    }

    /**
     * État pour un client inactif
     */
    public function inactif(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => User::STATUT_INACTIF
            ];
        });
    }

    /**
     * État pour un utilisateur non vérifié
     */
    public function unverified(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * État pour un client avec un numéro de téléphone Orange.
     */
    public function orangeMoney(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'telephone' => '+221 77 ' . 
                    str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT) . ' ' .
                    str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT) . ' ' .
                    str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT),
            ];
        });
    }

    /**
     * État pour un client avec un numéro de téléphone Free.
     */
    public function freeMoney(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'telephone' => '+221 76 ' . 
                    str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT) . ' ' .
                    str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT) . ' ' .
                    str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT),
            ];
        });
    }
}
