<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Création des transactions...');

        // Récupérer tous les clients
        $clients = User::where('type', 'client')->get();

        if ($clients->isEmpty()) {
            $this->command->error('Aucun client trouvé.');
            return;
        }

        foreach ($clients as $client) {
            $this->command->info("Création des transactions pour le client {$client->id}...");
            
            // Récupérer les comptes du client
            $comptes = Compte::where('client_id', $client->id)->get();
            
            if ($comptes->isEmpty()) {
                continue;
            }
            
            foreach ($comptes as $compte) {
                // 1. Dépôts initiaux (1-3 par compte)
                $nbDepots = rand(1, 3);
                for ($i = 0; $i < $nbDepots; $i++) {
                    Transaction::factory()
                        ->depot()
                        ->effectue()
                        ->create([
                            'compte_source_id' => $compte->id,
                            'montant' => fake()->randomFloat(2, 1000, 5000),
                            'created_at' => now()->subDays(rand(30, 60))
                        ]);
                }

                // 2. Retraits (2-4 par compte)
                $nbRetraits = rand(2, 4);
                for ($i = 0; $i < $nbRetraits; $i++) {
                    Transaction::factory()
                        ->retrait()
                        ->state([
                            'compte_source_id' => $compte->id,
                            'statut' => $this->getRandomStatut(0.75), // 75% de chance d'être effectué
                            'montant' => fake()->randomFloat(2, 50, 500),
                            'created_at' => now()->subDays(rand(1, 30))
                        ])
                        ->create();
                }
            }

            // 3. Virements entre comptes (pour 70% des clients)
            if (count($comptes) >= 2 && rand(1, 100) <= 70) {
                $nbVirements = rand(1, 3);
                for ($i = 0; $i < $nbVirements; $i++) {
                    $compteSource = $comptes->random();
                    $compteDestination = $comptes->except($compteSource->id)->random();

                    Transaction::factory()
                        ->virement()
                        ->state([
                            'compte_source_id' => $compteSource->id,
                            'compte_destination_id' => $compteDestination->id,
                            'montant' => fake()->randomFloat(2, 100, 1000),
                            'statut' => $this->getRandomStatut(0.70), // 70% de chance d'être effectué
                            'created_at' => now()->subDays(rand(1, 45))
                        ])
                        ->create();
                }
            }

            // 4. Transactions récentes en attente
            foreach ($comptes as $compte) {
                if (rand(1, 100) <= 40) { // 40% de chance par compte
                    Transaction::factory()
                        ->state([
                            'compte_source_id' => $compte->id,
                            'statut' => Transaction::STATUT_EN_ATTENTE,
                            'created_at' => now()->subDays(rand(0, 5))
                        ])
                        ->create();
                }
            }
        }
        
        $this->command->info('Transactions créées avec succès !');
    }

    /**
     * Obtenir un statut aléatoire avec une probabilité donnée d'être effectué
     */
    private function getRandomStatut(float $effectueProbability): string
    {
        $random = rand(1, 100) / 100;

        if ($random <= $effectueProbability) {
            return Transaction::STATUT_EFFECTUE;
        }

        return rand(0, 1) === 0 
            ? Transaction::STATUT_EN_ATTENTE 
            : Transaction::STATUT_ANNULE;
    }
}
