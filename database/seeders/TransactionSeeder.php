<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des transactions pour chaque compte bancaire existant (incluant les soft deleted)
        $comptes = \App\Models\CompteBancaire::withTrashed()->get();
        foreach ($comptes as $compte) {
            \App\Models\Transaction::factory()->count(5)->create([
                'compte_bancaire_id' => $compte->id,
            ]);
        }
    }
}
