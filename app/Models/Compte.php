<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Compte extends Model
{
    use HasFactory, HasUuids;

    // Désactiver l'auto-incrément
    public $incrementing = false;
    
    // Définir le type de la clé primaire
    protected $keyType = 'string';

    protected $fillable = [
        'numero',
        'solde',
        'type',
        'statut',
        'client_id',
    ];

    protected $casts = [
        'solde' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    /**
     * Obtenir le client propriétaire du compte
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')
                    ->where('type', User::TYPE_CLIENT);
    }

    protected static function booted(): void
    {
        static::creating(function ($compte) {
            do {
                // Générer un numéro de compte de 13 chiffres avec préfixe CPT
                $numero = 'CPT' . str_pad(random_int(1, 9999999999999), 13, '0', STR_PAD_LEFT);
                
                // Vérifier si ce numéro existe déjà
                $exists = static::where('numero', $numero)->exists();
            } while ($exists);

            // Assigner le numéro unique généré
            $compte->numero = $numero;
        });

        // Vérifier que seuls les clients peuvent avoir des comptes
        static::creating(function ($compte) {
            $user = User::find($compte->client_id);
            if (!$user || !$user->isClient()) {
                throw new \RuntimeException("Seuls les clients peuvent avoir des comptes");
            }
        });
    }
}
