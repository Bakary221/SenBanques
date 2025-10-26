<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'prenom' => 'Admin',
            'nom' => 'System',
            'login' => 'admin',
            'email' => 'admin@senbanque.com',
            'password' => bcrypt('password'),
            'statut' => 'actif',
            'role' => 'admin',
            'telephone' => '771234567',
            'adresse' => 'Dakar, Sénégal',
            'cni' => 'TEMP-ADMIN-001',
        ]);

        // Créer un utilisateur client
        User::create([
            'prenom' => 'Client',
            'nom' => 'Test',
            'login' => 'client',
            'email' => 'client@senbanque.com',
            'password' => bcrypt('password'),
            'statut' => 'actif',
            'role' => 'client',
            'telephone' => '781234567',
            'adresse' => 'Dakar, Sénégal',
            'cni' => 'TEMP-CLIENT-001',
        ]);

        // Créer d'autres utilisateurs de test
        User::factory()->count(8)->create();
    }
}
