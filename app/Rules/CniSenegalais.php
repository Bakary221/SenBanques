<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CniSenegalais implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Nettoyer la valeur (supprimer espaces)
        $cni = trim($value);

        // Vérifier que c'est exactement 13 chiffres
        if (!is_numeric($cni) || strlen($cni) !== 13) {
            $fail('Le numéro CNI doit contenir exactement 13 chiffres.');
            return;
        }
    }
}