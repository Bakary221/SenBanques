<?php

namespace App\Jobs;

use App\Models\CompteBancaire;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GererArchivageComptes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Début du job de gestion de l\'archivage des comptes');

        // Archiver les comptes épargne bloqués dont la date de début de blocage est dépassée
        $comptesAArchiver = CompteBancaire::withoutGlobalScopes()
            ->where('statut', 'bloque')
            ->where('type_compte', 'Epargne')
            ->where('statut_archive', 'actif')
            ->whereNotNull('date_debut_blocage')
            ->where('date_debut_blocage', '<=', now()->subDays(30)) // Archiver après 30 jours de blocage
            ->get();

        foreach ($comptesAArchiver as $compte) {
            $compte->archiver();
            Log::info("Compte {$compte->numero} archivé automatiquement");
        }

        // Désarchiver les comptes épargne bloqués dont la date de fin de blocage est atteinte
        $comptesADesarchiver = CompteBancaire::withoutGlobalScopes()
            ->where('statut', 'bloque')
            ->where('type_compte', 'Epargne')
            ->where('statut_archive', 'archive')
            ->whereNotNull('date_fin_blocage')
            ->where('date_fin_blocage', '<=', now())
            ->get();

        foreach ($comptesADesarchiver as $compte) {
            $compte->desarchiver();
            Log::info("Compte {$compte->numero} désarchivé automatiquement");
        }

        Log::info("Job terminé : {$comptesAArchiver->count()} comptes archivés, {$comptesADesarchiver->count()} comptes désarchivés");
    }
}
