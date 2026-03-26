<?php

namespace App\Notifications;

use App\Models\Formulaire;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DossierEnvoyeAuChef extends Notification
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
            'title' => 'Nouveau dossier à traiter',
            'body' => sprintf(
                'Le dossier %s vous a été transmis pour instruction.',
                $this->formulaire->reference
            ),
            'formulaire_id' => $this->formulaire->id,
            'url' => route('chefService.forms.show', $this->formulaire),
        ];
    }
}
