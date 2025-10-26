<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \Illuminate\Support\Str;
use \App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompteBancaire>
 */
class CompteBancaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string)Str::uuid(),
            'numero' => null, // Le numéro sera généré automatiquement par le modèle
            'type_compte' => fake()->randomElement(['Epargne', 'Chéque']),
            'user_id' => User::factory(),
        ];
    }
}
