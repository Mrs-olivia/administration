<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulaire extends Model
{
    /** @use HasFactory<\Database\Factories\FormulaireFactory> */
    use HasFactory;
    const STATUS_EN_ATTENTE = 0;
    const STATUS_EN_COURS    = 1;
    const STATUS_TRAITE      = 2;
    const STATUS_REJETE      = 3;
    const STATUS_ARCHIVE     = 4;
    protected $fillable = [
        'service_code',
        'annee',
        'numero_ordre',
        'expediteur',
        'objet',
        'type_document',
        'date_reception',
        'date_echeance',
        'fichier',
        'status',
    ];
    protected $casts = [
        'date_reception' => 'date',
        'date_echeance' => 'date',
        'status'         => 'integer',
    ];

    // --- 2. Accesseur pour afficher la référence formatée facilement ---
    public function getReferenceAttribute()
    {
        return "{$this->service_code}/{$this->annee}-" . str_pad($this->numero_ordre, 4, '0', STR_PAD_LEFT);
    }
}
