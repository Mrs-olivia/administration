<?php

namespace App\Notifications;

use App\Models\Formulaire;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DossierEnvoyeAuSecretaire extends Notification
{
    use Queueable;

    public function __construct(public Formulaire $formulaire)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouveau formulaire du chef',
            'body' => sprintf(
                'Le formulaire %s a été créé par le chef de service et vous a été transmis.',
                $this->formulaire->reference
            ),
            'formulaire_id' => $this->formulaire->id,
            'url' => route('secretaire.forms.show', $this->formulaire),
        ];
    }
}

