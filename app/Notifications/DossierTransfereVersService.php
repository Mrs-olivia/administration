<?php

namespace App\Notifications;

use App\Models\Formulaire;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DossierTransfereVersService extends Notification
{
    use Queueable;

    public function __construct(public Formulaire $formulaire) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Dossier transféré vers votre service',
            'body' => sprintf(
                'Le dossier %s vous a été transmis pour instruction (transfert inter-services).',
                $this->formulaire->reference
            ),
            'formulaire_id' => $this->formulaire->id,
            'url' => route('secretaire.forms.show', $this->formulaire),
        ];
    }
}
