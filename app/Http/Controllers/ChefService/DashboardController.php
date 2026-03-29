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

        $user = auth()->user();
        $userId = (int) $user->id;
        $scope = function ($q) use ($user) {
            if ($user->service_code) {
                $q->where('service_code', $user->service_code);
            }
        };

        $sentScope = function ($q) use ($scope, $userId): void {
            $scope($q);
            $q->where(function ($inner) use ($userId): void {
                $inner->whereNotNull('sent_to_chef_at')
                    ->orWhere('created_by_user_id', $userId);
            });
        };

        $counts = [];
        foreach (array_keys($statusLabels) as $status) {
            $counts[$status] = Formulaire::query()
                ->where('status', $status)
                ->tap($sentScope)
                ->count();
        }

        $aTraiter = Formulaire::query()
            ->whereIn('status', [
                Formulaire::STATUS_EN_ATTENTE,
                Formulaire::STATUS_EN_COURS,
            ])
            ->tap($sentScope)
            ->count();

        $recent = Formulaire::query()
            ->whereIn('status', [
                Formulaire::STATUS_EN_ATTENTE,
                Formulaire::STATUS_TRAITE,
                Formulaire::STATUS_REJETE,
            ])
            ->tap($sentScope)
            ->latest()
            ->limit(8)
            ->get();

        $traitesCount = $counts[Formulaire::STATUS_TRAITE];

        return view('chefService.dashboard', compact('statusLabels', 'counts', 'aTraiter', 'recent', 'traitesCount'));
    }
}
