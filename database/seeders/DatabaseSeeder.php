<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Création du client OAuth...');
        $this->call(ClientSeeder::class);

        $this->command->info('Création des utilisateurs...');
        $this->call(UserSeeder::class);

        $this->command->info('Création des comptes...');
        $this->call(CompteSeeder::class);

        $this->command->info('Création des transactions...');
        $this->call(TransactionSeeder::class);
    }
}
