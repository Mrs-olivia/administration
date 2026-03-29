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

        $user = auth()->user();
        $scope = function ($q) use ($user) {
            if ($user->service_code) {
                $q->where('service_code', $user->service_code);
            }
        };

        $counts = [];
        foreach (array_keys($statusLabels) as $status) {
            $counts[$status] = Formulaire::query()
                ->where('status', $status)
                ->tap($scope)
                ->count();
        }

        $total = array_sum($counts);

        $transferredTotal = Formulaire::query()
            ->tap($scope)
            ->where('transfers_count', '>', 0)
            ->count();

        $recent = Formulaire::query()
            ->tap($scope)
            ->latest()
            ->limit(8)
            ->get();

        $transferredRecent = Formulaire::query()
            ->tap($scope)
            ->where('transfers_count', '>', 0)
            ->orderByDesc('last_transferred_at')
            ->limit(15)
            ->get();

        $pollDashboardIds = $recent->pluck('id')->merge($transferredRecent->pluck('id'))->unique()->values();

        return view('secretaire.dashboard', compact(
            'statusLabels',
            'counts',
            'total',
            'recent',
            'transferredTotal',
            'transferredRecent',
            'pollDashboardIds',
        ));
    }
}
