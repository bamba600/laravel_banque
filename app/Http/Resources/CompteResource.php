<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numeroCompte' => $this->numero,
            'titulaire' => $this->whenLoaded('client', function () {
                return $this->client ?
                    trim($this->client->prenom . ' ' . $this->client->name) :
                    'Client inconnu';
            }),
            'type' => $this->type,
            'solde' => (float) $this->solde,
            'devise' => 'FCFA',
            'dateCreation' => $this->created_at->toISOString(),
            'statut' => $this->statut,
            'motifBlocage' => $this->when($this->statut === 'bloque', function () {
                return $this->motifBlocage ?? 'Non spécifié';
            }),
            'metadata' => [
                'derniereModification' => $this->updated_at->toISOString(),
                'version' => 1
            ]
        ];
    }
}