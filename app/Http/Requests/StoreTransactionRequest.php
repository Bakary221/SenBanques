<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'compte_bancaire_id' => 'required|uuid|exists:compte_bancaires,id',
            'type' => 'required|in:depot,retrait,transfert',
            'montant' => 'required|numeric|min:0.01|max:999999999.99',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'compte_bancaire_id.required' => 'Le compte bancaire est obligatoire.',
            'compte_bancaire_id.uuid' => 'L\'ID du compte bancaire doit être un UUID valide.',
            'compte_bancaire_id.exists' => 'Le compte bancaire spécifié n\'existe pas.',
            'type.required' => 'Le type de transaction est obligatoire.',
            'type.in' => 'Le type de transaction doit être depot, retrait ou transfert.',
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant doit être supérieur à 0.',
            'montant.max' => 'Le montant ne peut pas dépasser 999 999 999.99.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'compte_bancaire_id' => 'compte bancaire',
            'type' => 'type de transaction',
            'montant' => 'montant',
        ];
    }
}