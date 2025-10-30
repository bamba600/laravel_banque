<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Archiver les comptes dont la date de début est arrivée, toutes les heures
        $schedule->command('comptes:archive-expired')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Débloquer les comptes dont la date de fin est arrivée, toutes les heures
        $schedule->command('comptes:unlock-expired')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
