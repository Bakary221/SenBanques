<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompteBancaireRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'numero' => 'nullable|string|unique:compte_bancaires,numero|regex:/^C\d{6}$/',
            'type_compte' => 'required|in:Epargne,Chéque',
            'user_id' => 'required|uuid|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'numero.unique' => 'Ce numéro de compte est déjà utilisé.',
            'numero.regex' => 'Le numéro de compte doit être au format C suivi de 6 chiffres (ex: C001234).',
            'type_compte.required' => 'Le type de compte est obligatoire.',
            'type_compte.in' => 'Le type de compte doit être Epargne ou Chéque.',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.uuid' => 'L\'ID utilisateur doit être un UUID valide.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'numero' => 'numéro de compte',
            'type_compte' => 'type de compte',
            'user_id' => 'utilisateur',
        ];
    }
}