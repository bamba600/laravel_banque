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
        Schema::table('users', function (Blueprint $table) {
            $table->string('prenom')->nullable()->after('name');
            $table->string('telephone')->unique()->nullable()->after('email');
            $table->string('adresse')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->nullable();
            
            // Index pour optimiser les requêtes
            $table->index(['type', 'statut']);
            $table->index('telephone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['prenom', 'telephone', 'adresse', 'statut']);
            // Les index sont supprimés automatiquement avec les colonnes
        });
    }
};
