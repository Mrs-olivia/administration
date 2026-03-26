<?php

namespace App\Http\Controllers\Secretaire;

use App\Http\Controllers\Controller;
use App\Models\WorkflowLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkflowLogController extends Controller
{
    public function index(Request $request): View
    {
        $expediteur = trim((string) $request->query('expediteur', ''));

        $query = WorkflowLog::query()
            ->with(['formulaire'])
            ->latest();

        if ($expediteur !== '') {
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->whereHas('formulaire', function ($q) use ($expediteur, $operator): void {
                $q->where('expediteur', $operator, '%'.$expediteur.'%');
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('secretaire.workflow_logs.index', compact('logs', 'expediteur'));
    }
}

