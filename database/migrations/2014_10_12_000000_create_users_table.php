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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prenom');
            $table->string('nom');
            $table->string('login')->unique();
            $table->string('email')->unique();
            $table->enum('statut' , ['actif' , 'inactif']);
            $table->string('cni')->unique();
            $table->string('code');
            $table->string('telephone');
            $table->string('adresse');
            $table->string('password');

            $table->timestamps();

            // Index pour les performances
            $table->index(['statut']);
            $table->index(['email']);
            $table->index(['login']);
            $table->index(['cni']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
