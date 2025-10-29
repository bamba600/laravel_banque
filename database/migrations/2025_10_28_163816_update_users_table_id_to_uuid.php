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
            // Supprimer l'ancienne colonne id auto-increment
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Ajouter la nouvelle colonne UUID
            $table->uuid('id')->first()->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la colonne UUID
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Remettre l'ancienne colonne auto-increment
            $table->id();
        });
    }
};
