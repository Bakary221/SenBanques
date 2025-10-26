<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneSenegalais implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier que c'est exactement 9 chiffres
        if (!is_numeric($value) || strlen($value) !== 9) {
            $fail('Le numéro de téléphone doit contenir exactement 9 chiffres.');
            return;
        }

        // Vérifier que le numéro commence par les préfixes autorisés
        $prefixes = ['77', '78', '70', '75', '76'];
        $numeroPrefix = substr($value, 0, 2);

        if (!in_array($numeroPrefix, $prefixes)) {
            $fail('Le numéro de téléphone doit commencer par 77, 78, 70, 75 ou 76.');
            return;
        }
    }
}