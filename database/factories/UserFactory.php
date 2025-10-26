<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'login' => fake()->unique()->userName(),
            'statut' => fake()->randomElement(['actif' , 'inactif']),
            'cni' => fake()->unique()->numerify('########'),
            'code' => 'USR-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'telephone' => fake()->unique()->phoneNumber(),
            'adresse' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

}
