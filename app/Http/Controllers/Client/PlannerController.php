<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Planner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Wedding Planner milik client: ringkasan, checklist, anggaran, tamu.
 * Semua data diakses lewat $this->planner(), jadi client hanya bisa menyentuh miliknya sendiri.
 */
class PlannerController extends Controller
{
    private const TABS = ['ringkasan', 'checklist', 'anggaran', 'tamu'];

    public function show(Request $request)
    {
        $planner = $this->planner();
        $planner->seedTasks();
        $planner->load(['tasks' => fn ($q) => $q->orderBy('due_date')->orderBy('id'), 'budgetItems', 'guests' => fn ($q) => $q->orderBy('name')]);

        $tab = in_array($request->query('tab'), self::TABS, true) ? $request->query('tab') : 'ringkasan';

        $tasks = $planner->tasks;
        $budgetItems = $planner->budgetItems;
        $guests = $planner->guests;
        $stats = $planner->stats();

        // Paket katalog relevan sebagai saran vendor di tab anggaran
        $saranPaket = Package::with('category:id,nama')->orderBy('harga')->get(['id', 'nama', 'harga', 'category_id']);

        return view('client.planner', compact('planner', 'tab', 'tasks', 'budgetItems', 'guests', 'stats', 'saranPaket'));
    }

    public function updateInfo(Request $request)
    {
        $data = $request->validate([
            'partner_1'    => 'nullable|string|max:100',
            'partner_2'    => 'nullable|string|max:100',
            'wedding_date' => 'nullable|date',
            'venue'        => 'nullable|string|max:150',
            'budget'       => 'nullable|numeric|min:0|max:9999999999999',
            'guest_target' => 'nullable|integer|min:0|max:100000',
        ]);
        $data['budget'] ??= 0;
        $data['guest_target'] ??= 0;

        $planner = $this->planner();
        $dateChanged = $planner->wedding_date?->toDateString() !== ($data['wedding_date'] ?? null);
        $planner->update($data);
        if ($dateChanged) {
            $planner->rescheduleTasks();
        }

        return $this->back('ringkasan', 'Rencana pernikahan disimpan.');
    }

    /**
     * Hapus planner beserta semua tugas, anggaran, dan tamu (FK cascade), lalu mulai dari awal.
     * Wajib ketik "RESET" supaya tidak terhapus karena salah klik.
     */
    public function reset(Request $request)
    {
        $request->validate(['confirm' => 'required|in:RESET'], [
            'confirm.required' => 'Ketik RESET untuk mengonfirmasi.',
            'confirm.in'       => 'Ketik RESET (huruf besar) untuk mengonfirmasi.',
        ]);

        Planner::where('client_id', Auth::guard('client')->id())->delete();

        return $this->back('ringkasan', 'Planner direset. Semua data lama sudah dihapus, kamu bisa mulai dari awal.');
    }

    // ---------- Checklist ----------

    public function storeTask(Request $request)
    {
        $data = $this->validateTask($request);
        $planner = $this->planner();
        $data['due_date'] ??= $planner->dueFor($data['phase']);
        $planner->tasks()->create($data);

        return $this->back('checklist', 'Tugas ditambahkan.');
    }

    public function updateTask(Request $request, int $id)
    {
        $task = $this->planner()->tasks()->findOrFail($id);
        $data = $this->validateTask($request);
        $data['due_date'] ??= $this->planner()->dueFor($data['phase']);
        $task->update($data);

        return $this->back('checklist', 'Tugas diperbarui.');
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title'    => 'required|string|max:150',
            'note'     => 'nullable|string|max:2000',
            'phase'    => ['required', Rule::in(array_keys(Planner::PHASES))],
            'due_date' => 'nullable|date',
        ]);
    }

    public function toggleTask(int $id)
    {
        $task = $this->planner()->tasks()->findOrFail($id);
        $task->update(['done_at' => $task->done_at ? null : now()]);

        return $this->back('checklist');
    }

    public function destroyTask(int $id)
    {
        $this->planner()->tasks()->findOrFail($id)->delete();

        return $this->back('checklist', 'Tugas dihapus.');
    }

    // ---------- Anggaran ----------

    public function storeBudget(Request $request)
    {
        $this->planner()->budgetItems()->create($this->validateBudget($request));

        return $this->back('anggaran', 'Pos anggaran ditambahkan.');
    }

    public function updateBudget(Request $request, int $id)
    {
        $this->planner()->budgetItems()->findOrFail($id)->update($this->validateBudget($request));

        return $this->back('anggaran', 'Pos anggaran diperbarui.');
    }

    public function destroyBudget(int $id)
    {
        $this->planner()->budgetItems()->findOrFail($id)->delete();

        return $this->back('anggaran', 'Pos anggaran dihapus.');
    }

    // ---------- Tamu ----------

    public function storeGuest(Request $request)
    {
        $this->planner()->guests()->create($this->validateGuest($request));

        return $this->back('tamu', 'Tamu ditambahkan.');
    }

    public function updateGuest(Request $request, int $id)
    {
        $this->planner()->guests()->findOrFail($id)->update($this->validateGuest($request));

        return $this->back('tamu', 'Data tamu diperbarui.');
    }

    public function rsvpGuest(Request $request, int $id)
    {
        $data = $request->validate(['rsvp' => ['required', Rule::in(array_keys(Planner::RSVP))]]);
        $this->planner()->guests()->findOrFail($id)->update($data);

        return $this->back('tamu');
    }

    public function destroyGuest(int $id)
    {
        $this->planner()->guests()->findOrFail($id)->delete();

        return $this->back('tamu', 'Tamu dihapus.');
    }

    // ---------- Helper ----------

    private function planner(): Planner
    {
        $id = Auth::guard('client')->id();

        // client_id sengaja tidak fillable, jadi diisi manual (forceFill) saat planner pertama dibuat
        return Planner::where('client_id', $id)->first()
            ?? tap((new Planner)->forceFill(['client_id' => $id]))->save();
    }

    private function back(string $tab, ?string $msg = null)
    {
        $redirect = redirect()->route('client.planner', ['tab' => $tab]);

        return $msg ? $redirect->with('success', $msg) : $redirect;
    }

    private function validateBudget(Request $request): array
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(array_keys(Planner::BUDGET_CATEGORIES))],
            'name'     => 'required|string|max:150',
            'estimate' => 'required|numeric|min:0|max:9999999999999',
            'paid'     => 'nullable|numeric|min:0|lte:estimate',
            'note'     => 'nullable|string|max:255',
        ], ['paid.lte' => 'Jumlah dibayar tidak boleh melebihi estimasi.']);
        $data['paid'] ??= 0;

        return $data;
    }

    private function validateGuest(Request $request): array
    {
        return $request->validate([
            'name'  => 'required|string|max:120',
            'side'  => ['required', Rule::in(array_keys(Planner::SIDES))],
            'phone' => 'nullable|string|max:30',
            'pax'   => 'required|integer|min:1|max:20',
            'rsvp'  => ['nullable', Rule::in(array_keys(Planner::RSVP))],
        ]) + ['rsvp' => 'pending'];
    }
}
