<?php

namespace App\Console\Commands;

use App\Models\Compte;
use Illuminate\Console\Command;

class UnlockExpiredBlockages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comptes:unlock-expired';
    protected $description = 'Débloque les comptes dont la date de fin de blocage est arrivée';

    public function handle()
    {
        $this->info('Vérification des comptes à débloquer...');

        $comptesADebloquer = Compte::where('statut', 'bloque')
            ->whereNotNull('date_fin_blockage')
            ->where('date_fin_blockage', '<=', now())
            ->get();

        $count = 0;
        foreach ($comptesADebloquer as $compte) {
            $compte->debloquer();
            $count++;
            $this->line("Compte {$compte->numero} débloqué");
        }

        $this->info("Déblocage terminé : {$count} comptes débloqués");

        return Command::SUCCESS;
    }
}
