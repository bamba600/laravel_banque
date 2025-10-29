<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Index pour les champs spécifiques aux clients dans la table users
        Schema::table('users', function (Blueprint $table) {
            // Index sur prenom pour les recherches de clients par prénom
            $table->index('prenom');

            // Index sur adresse pour filtrer les clients par localisation
            $table->index('adresse');
        });

        // Index pour la table comptes (liée aux clients)
        Schema::table('comptes', function (Blueprint $table) {
            // Index sur numero pour les recherches de comptes par numéro
            $table->index('numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['prenom']);
            $table->dropIndex(['adresse']);
        });

        Schema::table('comptes', function (Blueprint $table) {
            $table->dropIndex(['numero']);
        });
    }
};
