<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
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
        'motifBlocage',
        'date_debut_blockage',
        'date_fin_blockage',
        'client_id',
    ];

    protected $casts = [
        'solde' => 'decimal:2',
        'date_debut_blockage' => 'datetime',
        'date_fin_blockage' => 'datetime',
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

    /**
     * Scope global pour récupérer uniquement les comptes non archivés
     *
     * Utilisation: Compte::nonArchive()->get()
     *
     * @param Builder $query
     * @return Builder
     */
    protected static function scopeNonArchive(Builder $query): Builder
    {
        return $query->where('statut', '!=', 'archive');
    }

    /**
     * Scope local pour récupérer un compte par son numéro
     *
     * Utilisation: Compte::numero('CPT0000000000001')->first()
     *
     * @param Builder $query
     * @param string $numero
     * @return Builder
     */
    public function scopeNumero(Builder $query, string $numero): Builder
    {
        return $query->where('numero', $numero);
    }

    /**
     * Scope local pour récupérer les comptes d'un client basé sur son téléphone
     *
     * Utilisation: Compte::client('+221771234567')->get()
     *
     * @param Builder $query
     * @param string $telephone
     * @return Builder
     */
    public function scopeClient(Builder $query, string $telephone): Builder
    {
        return $query->whereHas('client', function (Builder $clientQuery) use ($telephone) {
            $clientQuery->where('telephone', $telephone);
        });
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

    /**
     * Bloquer un compte avec date de début
     */
    public function bloquer(string $motif, ?Carbon $dateDebut = null): bool
    {
        $this->update([
            'statut' => 'bloque',
            'motifBlocage' => $motif,
            'date_debut_blockage' => $dateDebut ?? now(),
        ]);

        return true;
    }

    /**
     * Archiver un compte et ses transactions
     */
    public function archiver(): bool
    {
        DB::transaction(function () {
            // Archiver les transactions liées
            $this->transactions()->update(['archived_at' => now()]);

            // Archiver le compte
            $this->update(['statut' => 'archive']);
        });

        return true;
    }

    /**
     * Vérifier si le blocage doit être activé (date de début arrivée)
     */
    public function blocageDoitActiver(): bool
    {
        return $this->date_debut_blockage && $this->date_debut_blockage->isPast() && $this->statut === 'bloque';
    }

    /**
     * Relation avec les transactions
     */
    /**
     * Débloquer un compte (remettre à actif)
     */
    public function debloquer(): bool
    {
        $this->update([
            'statut' => 'actif',
            'motifBlocage' => null,
            'date_debut_blockage' => null,
            'date_fin_blockage' => null,
        ]);

        return true;
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'compte_source_id')
                    ->orWhere('compte_destination_id', $this->id);
    }
}
