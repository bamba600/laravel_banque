<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    // Types d'utilisateurs
    public const TYPE_CLIENT = 'client';
    public const TYPE_ADMIN = 'admin';

    // Statuts possibles
    public const STATUT_ACTIF = 'actif';
    public const STATUT_INACTIF = 'inactif';

    /**
     * Champs de base requis pour tous les utilisateurs
     */
    protected static $baseFields = [
        'name',
        'email',
        'password',
        'type'
    ];

    /**
     * Champs supplémentaires requis selon le type d'utilisateur
     */
    protected static $requiredFieldsByType = [
        self::TYPE_CLIENT => [
            'prenom',
            'telephone',
            'adresse',
            'statut'
        ],
        self::TYPE_ADMIN => [] // Utilise uniquement les champs de base
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Champs de base
        'name',
        'email',
        'password',
        'type',
        
        // Champs spécifiques client
        'prenom',
        'telephone',
        'adresse',
        'statut'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Vérifie si l'utilisateur est un client
     */
    public function isClient(): bool
    {
        return $this->type === self::TYPE_CLIENT;
    }

    /**
     * Vérifie si l'utilisateur est un admin
     */
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * Obtenir tous les comptes de l'utilisateur (uniquement pour les clients)
     */
    public function comptes()
    {
        if (!$this->isClient()) {
            throw new \RuntimeException("Seuls les clients peuvent avoir des comptes");
        }
        return $this->hasMany(Compte::class, 'client_id');
    }
}
