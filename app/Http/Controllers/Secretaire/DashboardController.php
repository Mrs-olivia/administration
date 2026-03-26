<?php

namespace App\Http\Controllers\Secretaire;

use App\Http\Controllers\Controller;
use App\Models\Formulaire;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('dashboard.secretaire'), 403);

        $statusLabels = [
            Formulaire::STATUS_EN_ATTENTE => 'En attente',
            Formulaire::STATUS_EN_COURS => 'En cours de traitement',
            Formulaire::STATUS_TRAITE => 'Traité / Validé',
            Formulaire::STATUS_REJETE => 'Rejeté',
            Formulaire::STATUS_ARCHIVE => 'Archivé',
        ];

        $counts = [];
        foreach (array_keys($statusLabels) as $status) {
            $counts[$status] = Formulaire::where('status', $status)->count();
        }

        $total = array_sum($counts);

        $recent = Formulaire::query()
            ->latest()
            ->limit(8)
            ->get();

        return view('secretaire.dashboard', compact('statusLabels', 'counts', 'total', 'recent'));
    }
}
