<?php

namespace App\Exceptions;

/**
 * Exception pour les soldes insuffisants
 */
class SoldeInsuffisantException extends ApiException
{
    public function __construct(float $soldeActuel, float $montantDemande)
    {
        $message = "Solde insuffisant. Solde actuel: {$soldeActuel} FCFA, Montant demandé: {$montantDemande} FCFA";

        parent::__construct($message, 402, 'SOLDE_INSUFFISANT', [
            'solde_actuel' => $soldeActuel,
            'montant_demande' => $montantDemande,
            'difference' => $montantDemande - $soldeActuel
        ]);
    }
}