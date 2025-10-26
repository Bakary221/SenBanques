<?php

namespace App\Http\Requests;

use App\Rules\CniSenegalais;
use App\Rules\TelephoneSenegalais;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $rules = [
            'numero' => 'nullable|string|unique:compte_bancaires,numero|regex:/^C\d{6}$/',
            'type_compte' => 'required|in:Epargne,Chéque',
            'solde_initial' => 'required|numeric|min:10000',
            'nouveau_client' => 'boolean',
        ];

        // Validation conditionnelle pour les champs client
        if ($this->input('nouveau_client', false)) {
            $rules['prenom'] = 'required|string|max:255';
            $rules['nom'] = 'required|string|max:255';
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ];
            $rules['telephone'] = [
                'required',
                new TelephoneSenegalais(),
            ];
            $rules['adresse'] = 'required|string|max:500';
            $rules['profession'] = 'required|string|max:255';
            $rules['cni'] = [
                'nullable',
                new CniSenegalais(),
            ];
        } else {
            $rules['user_id'] = 'required|uuid|exists:users,id';
        }

        return $rules;
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
            'solde_initial.required' => 'Le solde initial est obligatoire.',
            'solde_initial.numeric' => 'Le solde initial doit être un nombre.',
            'solde_initial.min' => 'Le solde initial doit être d\'au moins 10 000 FCFA.',
            'nouveau_client.boolean' => 'Le champ nouveau client doit être vrai ou faux.',
            'prenom.required' => 'Le prénom est obligatoire pour un nouveau client.',
            'nom.required' => 'Le nom est obligatoire pour un nouveau client.',
            'email.required' => 'L\'email est obligatoire pour un nouveau client.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire pour un nouveau client.',
            'adresse.required' => 'L\'adresse est obligatoire pour un nouveau client.',
            'profession.required' => 'La profession est obligatoire pour un nouveau client.',
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
            'solde_initial' => 'solde initial',
            'nouveau_client' => 'nouveau client',
            'prenom' => 'prénom',
            'nom' => 'nom',
            'email' => 'email',
            'telephone' => 'téléphone',
            'adresse' => 'adresse',
            'profession' => 'profession',
            'cni' => 'CNI',
            'user_id' => 'utilisateur',
        ];
    }
}