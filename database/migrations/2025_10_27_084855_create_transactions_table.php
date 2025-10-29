<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            // Identifiant et numéro de référence
            $table->uuid('id')->primary();
            $table->string('numero')->unique();
            
            // Détails de la transaction
            $table->decimal('montant', 12, 2);
            $table->enum('type', ['depot', 'retrait', 'virement']);
            $table->enum('statut', ['en_attente', 'effectue', 'annule'])
                  ->default('en_attente');
            
            // Relations avec les comptes
            $table->uuid('compte_source_id');
            $table->uuid('compte_destination_id')->nullable();
            
            // Description optionnelle
            $table->text('description')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('compte_source_id')
                  ->references('id')
                  ->on('comptes')
                  ->onDelete('restrict');
                  
            $table->foreign('compte_destination_id')
                  ->references('id')
                  ->on('comptes')
                  ->onDelete('restrict');
                  
            // Index pour les recherches fréquentes
            $table->index('type');
            $table->index('statut');
            $table->index(['type', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
