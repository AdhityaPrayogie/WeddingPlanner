<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Planner;
use Illuminate\Http\Request;

/**
 * Admin melihat planner milik client (read-only), untuk follow-up & tawaran paket.
 */
class PlannerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $planners = Planner::query()
            ->with('client:id,name,username,email,phone')
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($t) => $t->whereNotNull('done_at'),
                'guests',
            ])
            ->withSum('budgetItems as estimate_sum', 'estimate')
            ->withSum('guests as guest_pax_sum', 'pax')
            ->when($q, fn ($query) => $query->where(fn ($w) => $w
                ->where('partner_1', 'like', "%$q%")
                ->orWhere('partner_2', 'like', "%$q%")
                ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%$q%")->orWhere('username', 'like', "%$q%"))))
            // Pernikahan terdekat dulu, yang belum isi tanggal di akhir
            ->orderByRaw('wedding_date IS NULL, wedding_date')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total'    => Planner::count(),
            'upcoming' => Planner::whereBetween('wedding_date', [today(), today()->addMonths(3)])->count(),
            'no_date'  => Planner::whereNull('wedding_date')->count(),
        ];

        return view('admin.planner.index', compact('planners', 'summary', 'q'));
    }

    public function show(int $id)
    {
        $planner = Planner::with([
            'client',
            'tasks' => fn ($q) => $q->orderBy('due_date')->orderBy('id'),
            'budgetItems',
            'guests' => fn ($q) => $q->orderBy('name'),
        ])->findOrFail($id);

        return view('admin.planner.show', ['planner' => $planner, 'stats' => $planner->stats()]);
    }
}
