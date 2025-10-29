<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // À ajuster selon vos besoins d'autorisation
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Champs de base (requis pour tous)
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => $this->isMethod('PUT') ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'type' => ['required', Rule::in([User::TYPE_CLIENT, User::TYPE_ADMIN])],

            // Champs spécifiques au client
            'prenom' => [
                Rule::requiredIf(fn() => $this->input('type') === User::TYPE_CLIENT),
                'nullable',
                'string',
                'max:255'
            ],
            'telephone' => [
                Rule::requiredIf(fn() => $this->input('type') === User::TYPE_CLIENT),
                'nullable',
                'string',
                Rule::unique('users')->ignore($this->user),
                'regex:/^\+221 [7][0-9] [0-9]{3} [0-9]{2} [0-9]{2}$/' // Format: +221 7X XXX XX XX
            ],
            'adresse' => [
                Rule::requiredIf(fn() => $this->input('type') === User::TYPE_CLIENT),
                'nullable',
                'string'
            ],
            'statut' => [
                Rule::requiredIf(fn() => $this->input('type') === User::TYPE_CLIENT),
                'nullable',
                Rule::in([User::STATUT_ACTIF, User::STATUT_INACTIF])
            ],
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis',
            'email.required' => "L'adresse email est requise",
            'email.email' => 'Format email invalide',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.required' => 'Le mot de passe est requis',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères',
            'type.required' => 'Le type est requis',
            'type.in' => 'Type invalide',
            'prenom.required_if' => 'Le prénom est requis pour un client',
            'telephone.required_if' => 'Le téléphone est requis pour un client',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'telephone.regex' => 'Format de téléphone invalide (+221 7X XXX XX XX)',
            'adresse.required_if' => "L'adresse est requise pour un client",
            'statut.required_if' => 'Le statut est requis pour un client',
            'statut.in' => 'Statut invalide'
        ];
    }
}
