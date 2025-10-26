<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompteBancaireCree implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $utilisateur;
    public $compteBancaire;
    public $motDePasseTemporaire;

    /**
     * Create a new event instance.
     */
    public function __construct($utilisateur, $compteBancaire, $motDePasseTemporaire)
    {
        $this->utilisateur = $utilisateur;
        $this->compteBancaire = $compteBancaire;
        $this->motDePasseTemporaire = $motDePasseTemporaire;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('compte-bancaire-cree'),
        ];
    }
}