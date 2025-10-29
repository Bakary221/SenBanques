<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompteBancaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des comptes pour l'utilisateur client de test
        $clientUser = \App\Models\User::where('login', 'client')->first();
        if ($clientUser) {
            \App\Models\CompteBancaire::factory()->count(3)->create([
                'user_id' => $clientUser->id,
                'statut' => 'actif'
            ]);
        }

        // Créer d'autres comptes de test seulement en développement
        if (app()->environment('local')) {
            \App\Models\CompteBancaire::factory()->count(17)->create();
        }
    }
}
