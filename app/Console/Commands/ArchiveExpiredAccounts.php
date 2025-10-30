<?php

namespace App\Console\Commands;

use App\Models\Compte;
use Illuminate\Console\Command;

class ArchiveExpiredAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comptes:archive-expired';
    protected $description = 'Archive les comptes dont la date de début de blocage est arrivée';

    public function handle()
    {
        $this->info('Vérification des comptes à archiver...');

        $comptesAArchiver = Compte::where('statut', 'bloque')
            ->whereNotNull('date_debut_blockage')
            ->where('date_debut_blockage', '<=', now())
            ->get();

        $count = 0;
        foreach ($comptesAArchiver as $compte) {
            $compte->archiver();
            $count++;
            $this->line("Compte {$compte->numero} archivé");
        }

        $this->info("Archivage terminé : {$count} comptes archivés");

        return Command::SUCCESS;
    }
}
