<?php

namespace App\Http\Requests;

use App\Rules\CniSenegalais;
use App\Rules\TelephoneSenegalais;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'email' => 'required|email|max:255|unique:users,email',
            'statut' => 'required|in:actif,inactif',
            'cni' => ['required', 'string', 'unique:users,cni', new CniSenegalais()],
            'code' => 'required|string|max:255',
            'telephone' => ['required', 'string', new TelephoneSenegalais()],
            'adresse' => 'required|string|max:500',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être actif ou inactif.',
            'cni.required' => 'Le numéro CNI est obligatoire.',
            'cni.unique' => 'Ce numéro CNI est déjà utilisé.',
            'code.required' => 'Le code est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'prenom' => 'prénom',
            'nom' => 'nom',
            'login' => 'login',
            'email' => 'email',
            'statut' => 'statut',
            'cni' => 'numéro CNI',
            'code' => 'code',
            'telephone' => 'numéro de téléphone',
            'adresse' => 'adresse',
            'password' => 'mot de passe',
        ];
    }
}