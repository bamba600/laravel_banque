<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    /**
     * Désactiver l'auto-incrément car on utilise les UUIDs
     */
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($transaction) {
            do {
                // Générer un numéro de transaction de format TRX-XXXXX
                $numero = 'TRX-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
                
                // Vérifier si ce numéro existe déjà
                $exists = static::where('numero', $numero)->exists();
            } while ($exists);

            // Assigner le numéro unique généré
            $transaction->numero = $numero;
        });
    }
    
    /**
     * Spécifier le type de la clé primaire
     */
    protected $keyType = 'string';

    /**
     * Les types de transactions possibles
     */
    const TYPE_DEPOT = 'depot';
    const TYPE_RETRAIT = 'retrait';
    const TYPE_VIREMENT = 'virement';

    /**
     * Les statuts possibles
     */
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EFFECTUE = 'effectue';
    const STATUT_ANNULE = 'annule';

    /**
     * Les attributs assignables en masse
     */
    protected $fillable = [
        'numero',
        'montant',
        'type',
        'statut',
        'compte_source_id',
        'compte_destination_id',
        'description'
    ];

    /**
     * Les conversions de types automatiques
     */
    protected $casts = [
        'montant' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec le compte source
     * C'est le compte d'où part l'argent (retrait/virement) 
     * ou qui reçoit l'argent (dépôt)
     */
    public function compteSource(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_source_id')
                    ->withTrashed(); // Permet d'accéder aux comptes supprimés
    }

    /**
     * Relation avec le compte destination (pour les virements)
     * C'est le compte qui reçoit l'argent lors d'un virement
     */
    public function compteDestination(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_destination_id')
                    ->withTrashed(); // Permet d'accéder aux comptes supprimés
    }

    /**
     * Vérifie si la transaction est un virement
     */
    public function isVirement(): bool
    {
        return $this->type === self::TYPE_VIREMENT;
    }

    /**
     * Vérifie si la transaction est terminée
     */
    public function isTerminee(): bool
    {
        return $this->statut === self::STATUT_EFFECTUE;
    }

    /**
     * Obtient le compte impacté selon le type de transaction
     */
    public function getCompteImpacte()
    {
        return match($this->type) {
            self::TYPE_DEPOT => $this->compteSource,
            self::TYPE_RETRAIT => $this->compteSource,
            self::TYPE_VIREMENT => $this->isDebit() ? $this->compteSource : $this->compteDestination,
        };
    }

    /**
     * Détermine si la transaction est un débit pour un compte donné
     */
    public function isDebit(): bool
    {
        return in_array($this->type, [self::TYPE_RETRAIT, self::TYPE_VIREMENT]);
    }
}
