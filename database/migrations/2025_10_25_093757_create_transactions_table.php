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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compte_bancaire_id');
            $table->enum('type' , ['depot' , 'retrait' , 'transfert']);
            $table->decimal('montant' , 15 , 2);

            $table->timestamps();

            // relation avec la table compte
            $table->foreign('compte_bancaire_id')->references('id')->on('compte_bancaires')->onDelete('cascade');

            // Index pour les performances
            $table->index(['compte_bancaire_id']);
            $table->index(['type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
