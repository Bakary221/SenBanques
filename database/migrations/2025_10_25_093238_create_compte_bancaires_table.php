<?php

use Illuminate\Database\Eloquent\Relations\Relation;
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
        Schema::create('compte_bancaires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero')->unique();
            $table->enum('type_compte' , ['Epargne' , 'Chéque'])->default('Epargne');
            $table->enum('statut', ['actif', 'inactif', 'bloque'])->default('actif');
            $table->text('motif_blocage')->nullable();
            $table->uuid('user_id'); // Clé étrangère UUID

            $table->timestamps();

            // Relation avec la table users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index pour les performances
            $table->index(['numero']);
            $table->index(['type_compte']);
            $table->index(['statut']);
            $table->index(['user_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compte_bancaires');
    }
};
