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
        $this->command->info('Création des utilisateurs...');
        $this->call(UserSeeder::class);

        // Les autres seeders seront ajoutés ici plus tard
        // $this->call(CompteSeeder::class);
        // $this->call(TransactionSeeder::class);
    }
}
