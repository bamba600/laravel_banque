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
            // Index for name since it's used for searching users
            $table->index('name');

            // Index for email since it's used for authentication and lookups
            $table->index('email');

            // Index for email_verified_at since we filter verified users
            $table->index('email_verified_at');

            // Index for telephone since it's used for authentication and lookups
            $table->index('telephone');

            // Index for type since we frequently filter users by type
            $table->index('type');

            // Index for statut since we often filter active/inactive users
            $table->index('statut');

            // Composite index for type and statut since these are often used together
            $table->index(['type', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['email']);
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['telephone']);
            $table->dropIndex(['type']);
            $table->dropIndex(['statut']);
            $table->dropIndex(['type', 'statut']);
        });
    }
};
