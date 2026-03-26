<?php

namespace App\Events;

use App\Models\Formulaire;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Émis lorsque le statut ou le texte chef d’un dossier change (SSOT : table formulaires).
 * Peut être branché sur le broadcasting (Echo) plus tard ; le secrétariat utilise le polling HTTP.
 */
class DossierMisAJour
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Formulaire $formulaire)
    {
    }
}
