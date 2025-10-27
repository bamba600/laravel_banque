<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compte;
use App\Models\User;

class CompteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les clients
        $clients = User::where('type', 'client')->get();

        foreach ($clients as $client) {
            // Compte courant (obligatoire)
            Compte::factory()
                ->courant()
                ->for($client, 'client')
                ->create();

            // Compte épargne (70% de chance)
            if (rand(1, 100) <= 70) {
                Compte::factory()
                    ->epargne()
                    ->for($client, 'client')
                    ->create();
            }

            // Compte bloqué (20% de chance)
            if (rand(1, 100) <= 20) {
                Compte::factory()
                    ->bloque()
                    ->for($client, 'client')
                    ->create();
            }
        }
    }
}
