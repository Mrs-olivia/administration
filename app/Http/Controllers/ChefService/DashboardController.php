<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use App\Models\Formulaire;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('dashboard.chef'), 403);

        $statusLabels = [
            Formulaire::STATUS_EN_ATTENTE => 'En attente',
            Formulaire::STATUS_EN_COURS => 'En cours',
            Formulaire::STATUS_TRAITE => 'Traité',
            Formulaire::STATUS_REJETE => 'Rejeté',
            Formulaire::STATUS_ARCHIVE => 'Archivé',
        ];

        $counts = [];
        foreach (array_keys($statusLabels) as $status) {
            $counts[$status] = Formulaire::where('status', $status)->count();
        }

        $aTraiter = Formulaire::whereIn('status', [
            Formulaire::STATUS_EN_ATTENTE,
            Formulaire::STATUS_EN_COURS,
        ])->count();

        $recent = Formulaire::query()
            ->whereIn('status', [
                Formulaire::STATUS_EN_ATTENTE,
                Formulaire::STATUS_TRAITE,
                Formulaire::STATUS_REJETE,
            ])
            ->latest()
            ->limit(8)
            ->get();

        $traitesCount = $counts[Formulaire::STATUS_TRAITE];

        return view('chefService.dashboard', compact('statusLabels', 'counts', 'aTraiter', 'recent', 'traitesCount'));
    }
}
