<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'solde' => [
                'required',
                'numeric',
                'decimal:0,2', // Accepte jusqu'à 2 décimales
                'min:0',       // Pas de solde négatif
            ],
            'type' => [
                'required',
                Rule::in(['courant', 'epargne'])
            ],
            'statut' => [
                'required',
                Rule::in(['actif', 'bloque', 'ferme'])
            ],
            'client_id' => [
                'required',
                'uuid',        // Doit être un UUID valide
                'exists:users,id,type,' . User::TYPE_CLIENT
            ],
        ];

        // Le numéro est validé uniquement lors des mises à jour
        // car il est généré automatiquement à la création
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['numero'] = [
                'required',
                'string',
                'regex:/^CPT\d{13}$/',  // Format: CPT + 13 chiffres
                Rule::unique('comptes', 'numero')->ignore($this->compte)
            ];
        }

        return $rules;
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'solde.required' => 'Le solde est obligatoire',
            'solde.numeric' => 'Le solde doit être un nombre',
            'solde.decimal' => 'Le solde ne peut avoir que 2 décimales',
            'solde.min' => 'Le solde ne peut pas être négatif',
            
            'type.required' => 'Le type de compte est obligatoire',
            'type.in' => 'Le type doit être soit courant soit épargne',
            
            'statut.required' => 'Le statut est obligatoire',
            'statut.in' => 'Le statut doit être actif, bloque ou ferme',
            
            'client_id.required' => 'L\'ID du client est obligatoire',
            'client_id.uuid' => 'L\'ID du client doit être un UUID valide',
            'client_id.exists' => 'Le client spécifié n\'existe pas',
            
            'numero.regex' => 'Le format du numéro de compte est invalide',
            'numero.unique' => 'Ce numéro de compte existe déjà',
        ];
    }
}
