<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formulaire;
use App\Models\WorkflowLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class WorkflowLogController extends Controller
{
    public function index(Request $request): View
    {
        $formulaireId = $request->query('formulaire_id');
        $action = $request->query('action');
        $expediteur = trim((string) $request->query('expediteur', ''));

        $formulaire = null;
        if ($formulaireId !== null && $formulaireId !== '') {
            $formulaire = Formulaire::find($formulaireId);
        }

        $query = WorkflowLog::query()
            ->with(['formulaire', 'user'])
            ->latest();

        if ($formulaireId !== null && $formulaireId !== '') {
            $query->where('formulaire_id', (int) $formulaireId);
        }

        if ($action !== null && $action !== '') {
            $query->where('action', (string) $action);
        }

        if ($expediteur !== '') {
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->whereHas('formulaire', function ($q) use ($expediteur, $operator): void {
                $q->where('expediteur', $operator, '%'.$expediteur.'%');
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.workflow_logs.index', compact('logs', 'formulaire', 'formulaireId', 'action', 'expediteur'));
    }
}

