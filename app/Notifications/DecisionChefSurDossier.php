<?php

namespace App\Notifications;

use App\Models\Formulaire;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DecisionChefSurDossier extends Notification
{
    use Queueable;

    public function __construct(
        public Formulaire $formulaire,
        public string $type,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isValidation = $this->type === 'valide';

        return [
            'title' => $isValidation ? 'Dossier validé' : 'Dossier rejeté',
            'body' => sprintf(
                'Le dossier %s a été %s par le chef. Consultez l’annotation.',
                $this->formulaire->reference,
                $isValidation ? 'validé' : 'rejeté'
            ),
            'formulaire_id' => $this->formulaire->id,
            'url' => route('secretaire.forms.show', $this->formulaire),
        ];
    }
}
