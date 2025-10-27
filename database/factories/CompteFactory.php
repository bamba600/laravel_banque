<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Compte;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compte>
 */
class CompteFactory extends Factory
{
    protected $model = Compte::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solde' => $this->faker->randomFloat(2, 100, 10000),
            'type' => 'courant', // par défaut
            'statut' => 'actif', // par défaut
            'client_id' => User::factory()->state([
                'type' => User::TYPE_CLIENT
            ])
        ];
    }

    /**
     * Compte courant standard
     */
    public function courant()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'courant',
                'solde' => $this->faker->randomFloat(2, 0, 50000),
            ];
        });
    }

    /**
     * Compte épargne avec solde minimum plus élevé
     */
    public function epargne()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'epargne',
                'solde' => $this->faker->randomFloat(2, 1000, 100000),
            ];
        });
    }

    /**
     * Compte bloqué (pour les tests de restriction)
     */
    public function bloque()
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => 'bloque'
            ];
        });
    }

    /**
     * Compte avec solde minimum
     */
    public function soldeMinimum()
    {
        return $this->state(function (array $attributes) {
            return [
                'solde' => 0
            ];
        });
    }
}
