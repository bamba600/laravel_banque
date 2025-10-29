<?php

namespace Database\Factories;

use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'montant' => $this->faker->randomFloat(2, 10, 10000),
            'type' => $this->faker->randomElement([
                Transaction::TYPE_DEPOT,
                Transaction::TYPE_RETRAIT,
                Transaction::TYPE_VIREMENT
            ]),
            'statut' => Transaction::STATUT_EN_ATTENTE,
            'description' => $this->faker->sentence(),
            'compte_source_id' => Compte::factory(),
            'compte_destination_id' => null
        ];
    }

    /**
     * Définir la transaction comme un dépôt
     */
    public function depot(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Transaction::TYPE_DEPOT,
            'compte_destination_id' => null,
            'montant' => $this->faker->randomFloat(2, 10, 5000),
        ]);
    }

    /**
     * Définir la transaction comme un retrait
     */
    public function retrait(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Transaction::TYPE_RETRAIT,
            'compte_destination_id' => null,
            'montant' => $this->faker->randomFloat(2, 10, 1000),
        ]);
    }

    /**
     * Définir la transaction comme un virement
     */
    public function virement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Transaction::TYPE_VIREMENT,
            'compte_destination_id' => Compte::factory(),
            'montant' => $this->faker->randomFloat(2, 10, 3000),
        ]);
    }

    /**
     * Définir la transaction comme en attente
     */
    public function enAttente(): static
    {
        return $this->state([
            'statut' => Transaction::STATUT_EN_ATTENTE
        ]);
    }

    /**
     * Définir la transaction comme effectuée
     */
    public function effectue(): static
    {
        return $this->state([
            'statut' => Transaction::STATUT_EFFECTUE
        ]);
    }

    /**
     * Définir la transaction comme annulée
     */
    public function annule(): static
    {
        return $this->state([
            'statut' => Transaction::STATUT_ANNULE]);
    }

    /**
     * Définir un montant spécifique
     */
    public function montant(float $montant): static
    {
        return $this->state(fn (array $attributes) => [
            'montant' => $montant
        ]);
    }
}
