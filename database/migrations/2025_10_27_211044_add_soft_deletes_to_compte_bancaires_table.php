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
        Schema::table('compte_bancaires', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('date_debut_blocage')->nullable();
            $table->timestamp('date_fin_blocage')->nullable();
            $table->enum('statut_archive', ['actif', 'archive'])->default('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compte_bancaires', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['date_debut_blocage', 'date_fin_blocage', 'statut_archive']);
        });
    }
};
