<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Activer l'extension uuid-ossp si elle n'est pas déjà activée
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        Schema::table('comptes', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['client_id']);
        });
        Schema::table('comptes', function (Blueprint $table) {
            // Supprimer l'ancienne colonne id
            $table->dropColumn('id');
        });

        Schema::table('comptes', function (Blueprint $table) {
            // Ajouter la nouvelle colonne uuid comme clé primaire
            $table->uuid('id')->primary()->first();
            // Modifier le type de client_id en uuid
            DB::statement('ALTER TABLE comptes ALTER COLUMN client_id TYPE uuid USING (uuid_generate_v4())');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comptes', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->id(); // Remettre l'ancien type de id
            $table->bigInteger('client_id')->change();
        });
    }
};
