<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Planner extends Model
{
    // client_id sengaja tidak fillable (diisi forceFill di PlannerController::planner), jadi tidak bisa disusupkan lewat form
    protected $fillable = ['partner_1', 'partner_2', 'wedding_date', 'venue', 'budget', 'guest_target'];

    protected $casts = ['wedding_date' => 'date', 'budget' => 'decimal:2'];

    /** Tahap checklist: kunci => [label, bulan sebelum hari H]. */
    public const PHASES = [
        '12m' => ['12 bulan sebelum', 12],
        '9m'  => ['9 bulan sebelum', 9],
        '6m'  => ['6 bulan sebelum', 6],
        '3m'  => ['3 bulan sebelum', 3],
        '1m'  => ['1 bulan sebelum', 1],
        '1w'  => ['Minggu terakhir', 0.25],
        'day' => ['Hari H', 0],
    ];

    public const BUDGET_CATEGORIES = [
        'venue' => 'Gedung', 'catering' => 'Catering', 'dekorasi' => 'Dekorasi', 'busana' => 'Busana & Rias',
        'dokumentasi' => 'Foto & Video', 'hiburan' => 'Hiburan', 'undangan' => 'Undangan & Souvenir',
        'cincin' => 'Cincin & Mahar', 'lainnya' => 'Lainnya',
    ];

    public const SIDES = ['pria' => 'Pihak Pria', 'wanita' => 'Pihak Wanita', 'bersama' => 'Bersama'];
    public const RSVP = ['pending' => 'Belum Konfirmasi', 'hadir' => 'Hadir', 'tidak' => 'Tidak Hadir'];

    /** Checklist awal, dibuat sekali saat planner pertama kali dibuka. */
    public const TEMPLATE = [
        '12m' => ['Tentukan anggaran total', 'Tentukan tanggal & konsep acara', 'Susun daftar tamu kasar', 'Pilih & booking gedung'],
        '9m'  => ['Pilih paket dekorasi', 'Booking fotografer & videografer', 'Pilih catering & jadwalkan tes rasa', 'Tentukan busana pengantin'],
        '6m'  => ['Booking MUA', 'Pesan undangan', 'Urus dokumen KUA / catatan sipil', 'Pilih hiburan / musik'],
        '3m'  => ['Kirim undangan', 'Fitting busana pertama', 'Pesan souvenir', 'Pilih & pesan cincin'],
        '1m'  => ['Rekap konfirmasi kehadiran tamu', 'Fitting busana terakhir', 'Susun rundown acara', 'Lunasi pembayaran vendor'],
        '1w'  => ['Konfirmasi ulang semua vendor', 'Siapkan seserahan & mahar', 'Gladi bersih'],
        'day' => ['Serahkan rundown ke koordinator', 'Nikmati hari bahagia'],
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PlannerTask::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(PlannerBudgetItem::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(PlannerGuest::class);
    }

    /** Buat checklist template (sekali saja). */
    public function seedTasks(): void
    {
        if ($this->tasks()->exists()) {
            return;
        }

        $rows = [];
        foreach (self::TEMPLATE as $phase => $titles) {
            foreach ($titles as $title) {
                $rows[] = ['title' => $title, 'phase' => $phase, 'due_date' => $this->dueFor($phase)];
            }
        }
        $this->tasks()->createMany($rows);
    }

    /** Tanggal target sebuah tahap; null bila tanggal pernikahan belum diisi. */
    public function dueFor(string $phase): ?Carbon
    {
        if (!$this->wedding_date || !isset(self::PHASES[$phase])) {
            return null;
        }
        $months = self::PHASES[$phase][1];

        return $months >= 1
            ? $this->wedding_date->copy()->subMonthsNoOverflow((int) $months)
            : $this->wedding_date->copy()->subDays((int) round($months * 28));
    }

    /** Sesuaikan ulang tanggal target tugas template setelah tanggal pernikahan berubah. */
    public function rescheduleTasks(): void
    {
        foreach (array_keys(self::PHASES) as $phase) {
            $this->tasks()->where('phase', $phase)->update(['due_date' => $this->dueFor($phase)]);
        }
    }

    /** Ringkasan angka planner. Panggil setelah relasi tasks, budgetItems, guests di-load. */
    public function stats(): array
    {
        $tasks = $this->tasks;
        $budget = $this->budgetItems;
        $guests = $this->guests;

        return [
            'days_left'   => $this->daysLeft(),
            'tasks_done'  => $tasks->whereNotNull('done_at')->count(),
            'tasks_total' => $tasks->count(),
            'overdue'     => $tasks->filter->isOverdue()->count(),
            'estimate'    => (float) $budget->sum('estimate'),
            'paid'        => (float) $budget->sum('paid'),
            'guest_rows'  => $guests->count(),
            'guest_pax'   => (int) $guests->sum('pax'),
            'guest_hadir' => (int) $guests->where('rsvp', 'hadir')->sum('pax'),
            'guest_tidak' => (int) $guests->where('rsvp', 'tidak')->sum('pax'),
        ];
    }

    public function daysLeft(): ?int
    {
        return $this->wedding_date ? (int) now()->startOfDay()->diffInDays($this->wedding_date, false) : null;
    }
}
