<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // À adapter selon vos besoins de sécurité
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'montant' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0.01', // Montant minimum de 0.01
            ],

            'type' => [
                'required',
                Rule::in([
                    Transaction::TYPE_DEPOT,
                    Transaction::TYPE_RETRAIT,
                    Transaction::TYPE_VIREMENT
                ]),
            ],

            'statut' => [
                'sometimes',
                Rule::in([
                    Transaction::STATUT_EN_ATTENTE,
                    Transaction::STATUT_EFFECTUE,
                    Transaction::STATUT_ANNULE
                ]),
            ],

            'compte_source_id' => [
                'required',
                'uuid',
                'exists:comptes,id',
                function ($attribute, $value, $fail) {
                    $type = $this->input('type');
                    if ($type === Transaction::TYPE_RETRAIT) {
                        $compte = \App\Models\Compte::find($value);
                        if ($compte && $compte->solde < $this->input('montant')) {
                            $fail('Le solde du compte est insuffisant pour cette opération.');
                        }
                    }
                },
            ],

            'compte_destination_id' => [
                Rule::requiredIf(fn() => $this->input('type') === Transaction::TYPE_VIREMENT),
                'uuid',
                'exists:comptes,id',
                'different:compte_source_id',
            ],

            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.decimal' => 'Le montant ne peut avoir que 2 décimales maximum.',
            'montant.min' => 'Le montant minimum est de 0.01.',

            'type.required' => 'Le type de transaction est obligatoire.',
            'type.in' => 'Le type de transaction doit être depot, retrait ou virement.',

            'statut.in' => 'Le statut doit être en_attente, effectue ou annule.',

            'compte_source_id.required' => 'Le compte source est obligatoire.',
            'compte_source_id.exists' => 'Le compte source n\'existe pas.',
            'compte_source_id.uuid' => 'L\'identifiant du compte source n\'est pas valide.',

            'compte_destination_id.required' => 'Le compte destination est obligatoire pour un virement.',
            'compte_destination_id.exists' => 'Le compte destination n\'existe pas.',
            'compte_destination_id.different' => 'Le compte destination doit être différent du compte source.',
            'compte_destination_id.uuid' => 'L\'identifiant du compte destination n\'est pas valide.',

            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Définir le statut par défaut si non fourni
        if (!$this->has('statut')) {
            $this->merge([
                'statut' => Transaction::STATUT_EN_ATTENTE
            ]);
        }
    }
}