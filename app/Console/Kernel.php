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
        // Gestion des blocages de comptes (toutes les heures)
        $schedule->job(new \App\Jobs\GererBlocageComptes)->hourly();

        // Gestion de l'archivage des comptes (tous les jours à minuit)
        $schedule->job(new \App\Jobs\GererArchivageComptes)->daily();
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
