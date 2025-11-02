<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer la colonne id avec CASCADE pour supprimer les contraintes dépendantes
        DB::statement('ALTER TABLE comptes DROP COLUMN id CASCADE;');

        // Recréer la colonne UUID id
        Schema::table('comptes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
        });

        // Restaurer les clés étrangères
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('compte_source_id', 'transactions_compte_source_id_foreign')
                  ->references('id')
                  ->on('comptes')
                  ->onDelete('restrict');
            $table->foreign('compte_destination_id', 'transactions_compte_destination_id_foreign')
                  ->references('id')
                  ->on('comptes')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // Supprimer les clés étrangères
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_compte_source_id_foreign');
            $table->dropForeign('transactions_compte_destination_id_foreign');
        });

        // Supprimer la colonne UUID id
        DB::statement('ALTER TABLE comptes DROP COLUMN id CASCADE;');

        // Recréer en tant que colonne id entière
        Schema::table('comptes', function (Blueprint $table) {
            $table->id();
        });

        // Restaurer les clés étrangères
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('compte_source_id', 'transactions_compte_source_id_foreign')
                  ->references('id')
                  ->on('comptes')
                  ->onDelete('restrict');
            $table->foreign('compte_destination_id', 'transactions_compte_destination_id_foreign')
                  ->references('id')
                  ->on('comptes')
                  ->onDelete('restrict');
        });
    }
};