<?php

namespace App\Jobs;

use App\Models\CompteBancaire;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GererBlocageComptes implements ShouldQueue
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
        Log::info('Début du job de gestion des blocages de comptes');

        // Débloquer les comptes dont la date de fin de blocage est atteinte
        $comptesADedebloquer = CompteBancaire::withoutGlobalScopes()
            ->where('statut', 'bloque')
            ->where('type_compte', 'Epargne')
            ->whereNotNull('date_fin_blocage')
            ->where('date_fin_blocage', '<=', now())
            ->get();

        foreach ($comptesADedebloquer as $compte) {
            $compte->debloquer();
            Log::info("Compte {$compte->numero} débloqué automatiquement");
        }

        Log::info("Job terminé : {$comptesADedebloquer->count()} comptes débloqués");
    }
}
