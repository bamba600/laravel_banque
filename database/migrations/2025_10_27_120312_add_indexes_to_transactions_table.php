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
        Schema::table('transactions', function (Blueprint $table) {
            // Index composites additionnels pour les requêtes courantes
            $table->index(['compte_source_id', 'created_at']);
            $table->index(['compte_source_id', 'type']);
            $table->index(['compte_destination_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Supprimer les index composites
            $table->dropIndex(['transactions_compte_source_id_created_at_index']);
            $table->dropIndex(['transactions_compte_source_id_type_index']);
            $table->dropIndex(['transactions_compte_destination_id_type_index']);
        });
    }
};
