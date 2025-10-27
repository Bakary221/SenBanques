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
        // Créer un utilisateur admin avec identifiants fixes
        User::updateOrCreate(
            ['login' => 'admin'],
            [
                'prenom' => 'Admin',
                'nom' => 'System',
                'email' => 'admin@senbanque.com',
                'password' => bcrypt('admin123'),
                'statut' => 'actif',
                'role' => 'admin',
                'telephone' => '771234567',
                'adresse' => 'Dakar, Sénégal',
                'cni' => 'TEMP-ADMIN-001',
            ]
        );

        // Créer un utilisateur client avec identifiants fixes
        User::updateOrCreate(
            ['login' => 'client'],
            [
                'prenom' => 'Client',
                'nom' => 'Test',
                'email' => 'client@senbanque.com',
                'password' => bcrypt('client123'),
                'statut' => 'actif',
                'role' => 'client',
                'telephone' => '781234567',
                'adresse' => 'Dakar, Sénégal',
                'cni' => 'TEMP-CLIENT-001',
            ]
        );

        // Créer d'autres utilisateurs de test seulement en développement
        if (app()->environment('local')) {
            User::factory()->count(8)->create();
        }
    }
}
