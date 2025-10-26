<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListComptesRequest extends FormRequest
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
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
            'numero' => 'nullable|string',
            'telephone' => 'nullable|string',
            'statut' => 'nullable|in:actif,inactif,bloque',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'Le numéro de page doit être un entier.',
            'page.min' => 'Le numéro de page doit être au minimum 1.',
            'limit.integer' => 'La limite doit être un entier.',
            'limit.min' => 'La limite doit être au minimum 1.',
            'limit.max' => 'La limite ne peut pas dépasser 100.',
            'numero.string' => 'Le numéro de compte doit être une chaîne de caractères.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'statut.in' => 'Le statut doit être actif, inactif ou bloque.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'page' => 'numéro de page',
            'limit' => 'limite',
            'numero' => 'numéro de compte',
            'telephone' => 'numéro de téléphone',
            'statut' => 'statut',
        ];
    }
}