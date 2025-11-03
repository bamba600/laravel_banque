<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer les clients existants pour éviter les doublons
        DB::table('oauth_clients')->where('name', 'Banque Mobile App')->delete();

        // Créer un client pour l'application mobile
        Client::create([
            'name' => 'Banque Mobile App',
            'secret' => 'banque_mobile_secret_2024_secure_key', // Clé secrète pour l'app mobile
            'redirect' => 'http://localhost', // URL de redirection (non utilisée pour password grant)
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);
    }
}