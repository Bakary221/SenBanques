<?php

namespace App\Listeners;

use App\Events\CompteBancaireCree;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendClientNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CompteBancaireCree $event): void
    {
        try {
            Mail::send('emails.client-notification', [
                'utilisateur' => $event->utilisateur,
                'compteBancaire' => $event->compteBancaire,
                'motDePasseTemporaire' => $event->motDePasseTemporaire,
            ], function ($message) use ($event) {
                $message->to($event->utilisateur->email)
                        ->subject('Votre compte bancaire a été créé');
            });
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas la création du compte
            Log::error('Erreur lors de l\'envoi de l\'email de notification: ' . $e->getMessage());
        }
    }
}