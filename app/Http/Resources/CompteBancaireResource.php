<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompteBancaireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'numeroCompte' => $this->numero,
            'titulaire' => $this->user->prenom . ' ' . $this->user->nom,
            'type' => $this->type_compte,
            'solde' => $this->solde,
            'devise' => 'FCFA',
            'dateCreation' => $this->created_at->toISOString(),
            'statut' => $this->statut,
            'motifBlocage' => $this->motif_blocage,
            'statutArchive' => $this->statut_archive,
            'metadata' => [
                'derniereModification' => $this->updated_at->toISOString(),
                'version' => 1,
            ],
        ];

        // Ajouter les informations de blocage pour les comptes épargne
        if ($this->type_compte === 'Epargne') {
            $data['blocage'] = [
                'dateDebut' => $this->date_debut_blocage?->toISOString(),
                'dateFin' => $this->date_fin_blocage?->toISOString(),
                'motif' => $this->motif_blocage,
                'estBloque' => $this->estBloque(),
            ];
        }

        return $data;
    }
}