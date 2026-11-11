@extends('layouts.main')

@section('title', 'Wedding Planner | Takari')

@php
    use App\Models\Planner;
    $rupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $pct = fn ($a, $b) => $b > 0 ? min(100, (int) round($a / $b * 100)) : 0;
    $tabs = ['ringkasan' => 'Ringkasan', 'checklist' => 'Checklist', 'anggaran' => 'Anggaran', 'tamu' => 'Tamu'];
    $sisaAnggaran = (float) $planner->budget - $stats['estimate'];
@endphp

@section('content')
<div class="container section planner">
    <header class="page-header">
        <div>
            <span class="eyebrow">Wedding Planner</span>
            <h1 class="page-title">
                @if ($planner->partner_1 || $planner->partner_2)
                    {{ $planner->partner_1 ?: '…' }} <span class="amp">&amp;</span> {{ $planner->partner_2 ?: '…' }}
                @else
                    Rencana Pernikahan
                @endif
            </h1>
        </div>
        @if ($stats['days_left'] !== null)
            <div class="countdown" aria-label="Hitung mundur">
                @if ($stats['days_left'] > 0)
                    <span class="countdown-num">{{ $stats['days_left'] }}</span>
                    <span class="countdown-label">hari lagi<br>{{ $planner->wedding_date->translatedFormat('d M Y') }}</span>
                @elseif ($stats['days_left'] === 0)
                    <span class="countdown-label">Hari ini hari bahagiamu</span>
                @else
                    <span class="countdown-label">Selamat menempuh hidup baru</span>
                @endif
            </div>
        @endif
    </header>

    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <nav class="nav nav-tabs planner-tabs flex-nowrap overflow-auto" aria-label="Bagian planner">
        @foreach ($tabs as $key => $label)
            <a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="{{ route('client.planner', ['tab' => $key]) }}"
                @if ($tab === $key) aria-current="page" @endif>
                {{ $label }}
                @if ($key === 'checklist' && $stats['overdue']) <span class="tab-dot" aria-label="{{ $stats['overdue'] }} tugas terlambat">{{ $stats['overdue'] }}</span> @endif
            </a>
        @endforeach
    </nav>

    {{-- ================= RINGKASAN ================= --}}
    @if ($tab === 'ringkasan')
        <div class="row stat-grid row-cols-2 row-cols-lg-4">
            <div class="col"><div class="stat-card">
                <i class="bi bi-check2-square" aria-hidden="true"></i>
                <span class="stat-label">Checklist</span>
                <span class="stat-value">{{ $pct($stats['tasks_done'], $stats['tasks_total']) }}%</span>
                <div class="progress-line" role="progressbar" aria-label="Progres checklist" aria-valuenow="{{ $pct($stats['tasks_done'], $stats['tasks_total']) }}" aria-valuemin="0" aria-valuemax="100"><span style="--v: {{ $pct($stats['tasks_done'], $stats['tasks_total']) }}%"></span></div>
                <small class="text-muted">{{ $stats['tasks_done'] }} dari {{ $stats['tasks_total'] }} selesai</small>
            </div></div>
            <div class="col"><div class="stat-card">
                <i class="bi bi-wallet2" aria-hidden="true"></i>
                <span class="stat-label">Terbayar</span>
                <span class="stat-value">{{ $pct($stats['paid'], $stats['estimate']) }}%</span>
                <div class="progress-line" role="progressbar" aria-label="Progres pembayaran" aria-valuenow="{{ $pct($stats['paid'], $stats['estimate']) }}" aria-valuemin="0" aria-valuemax="100"><span style="--v: {{ $pct($stats['paid'], $stats['estimate']) }}%"></span></div>
                <small class="text-muted tabular">{{ $rupiah($stats['paid']) }}</small>
            </div></div>
            <div class="col"><div class="stat-card">
                <i class="bi bi-people" aria-hidden="true"></i>
                <span class="stat-label">Tamu Hadir</span>
                <span class="stat-value">{{ $stats['guest_hadir'] }}</span>
                <div class="progress-line" role="progressbar" aria-label="Konfirmasi tamu" aria-valuenow="{{ $pct($stats['guest_hadir'], $stats['guest_pax']) }}" aria-valuemin="0" aria-valuemax="100"><span style="--v: {{ $pct($stats['guest_hadir'], $stats['guest_pax']) }}%"></span></div>
                <small class="text-muted">dari {{ $stats['guest_pax'] }} orang diundang</small>
            </div></div>
            <div class="col"><div class="stat-card">
                <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                <span class="stat-label">Terlambat</span>
                <span class="stat-value">{{ $stats['overdue'] }}</span>
                <div class="progress-line"><span style="--v: 0%"></span></div>
                <small class="text-muted">tugas melewati target</small>
            </div></div>
        </div>

        <div class="row gy-4 gx-lg-5">
            <div class="col-lg-7">
                <form method="POST" action="{{ route('client.planner.info') }}" class="panel">
                    @csrf @method('PUT')
                    <h2 class="h4 mb-4">Detail Pernikahan</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="partner_1" class="form-label">Nama Pengantin 1</label>
                            <input type="text" id="partner_1" name="partner_1" class="form-control" maxlength="100" value="{{ old('partner_1', $planner->partner_1) }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="partner_2" class="form-label">Nama Pengantin 2</label>
                            <input type="text" id="partner_2" name="partner_2" class="form-control" maxlength="100" value="{{ old('partner_2', $planner->partner_2) }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="wedding_date" class="form-label">Tanggal Pernikahan</label>
                            <input type="date" id="wedding_date" name="wedding_date" class="form-control" value="{{ old('wedding_date', $planner->wedding_date?->toDateString()) }}" aria-describedby="wedding_date-help">
                            <div id="wedding_date-help" class="form-text">Target checklist dihitung dari tanggal ini.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="venue" class="form-label">Lokasi</label>
                            <input type="text" id="venue" name="venue" class="form-control" maxlength="150" value="{{ old('venue', $planner->venue) }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="budget" class="form-label">Total Anggaran (Rp)</label>
                            <input type="number" id="budget" name="budget" class="form-control" min="0" step="1" inputmode="numeric" value="{{ old('budget', (int) $planner->budget ?: '') }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="guest_target" class="form-label">Target Jumlah Tamu</label>
                            <input type="number" id="guest_target" name="guest_target" class="form-control" min="0" inputmode="numeric" value="{{ old('guest_target', $planner->guest_target ?: '') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Simpan</button>
                </form>
            </div>

            <div class="col-lg-5">
                <div class="panel">
                    <h2 class="h4 mb-4">Langkah Berikutnya</h2>
                    @php $next = $tasks->whereNull('done_at')->take(5); @endphp
                    @forelse ($next as $t)
                        <form method="POST" action="{{ route('client.planner.tasks.toggle', $t->id) }}" class="task-row">
                            @csrf @method('PATCH')
                            <button type="submit" class="task-check" aria-label="Tandai selesai: {{ $t->title }}"></button>
                            <div class="flex-grow-1 min-w-0">
                                <div>{{ $t->title }}</div>
                                @if ($t->note) <div class="task-note">{{ $t->note }}</div> @endif
                                @if ($t->due_date)
                                    <small class="{{ $t->isOverdue() ? 'text-danger' : 'text-muted' }} tabular">
                                        {{ $t->isOverdue() ? 'Terlambat · ' : '' }}{{ $t->due_date->translatedFormat('d M Y') }}
                                    </small>
                                @endif
                            </div>
                        </form>
                    @empty
                        <p class="text-muted mb-0">Semua tugas selesai.</p>
                    @endforelse
                    <a href="{{ route('client.planner', ['tab' => 'checklist']) }}" class="btn btn-link btn-sm mt-3">Lihat semua checklist</a>
                </div>
            </div>
        </div>

        {{-- Zona bahaya: reset planner --}}
        <section class="danger-zone" aria-labelledby="reset-title">
            <div>
                <h2 class="h5 mb-1" id="reset-title">Reset Planner</h2>
                <p class="text-muted small mb-0">Hapus semua detail, checklist, anggaran, dan daftar tamu, lalu mulai dari awal. Tidak bisa dibatalkan.</p>
            </div>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalReset">
                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset Planner
            </button>
        </section>

        <div class="modal fade" id="modalReset" tabindex="-1" aria-labelledby="modalResetLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('client.planner.reset') }}" class="modal-content">
                    @csrf @method('DELETE')
                    <div class="modal-header">
                        <h2 class="modal-title" id="modalResetLabel">Reset planner?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Data berikut akan <strong>dihapus permanen</strong>:</p>
                        <ul class="reset-list">
                            <li>Detail pernikahan (nama, tanggal, lokasi, anggaran)</li>
                            <li>{{ $stats['tasks_total'] }} tugas checklist beserta catatannya</li>
                            <li>{{ $budgetItems->count() }} pos anggaran</li>
                            <li>{{ $stats['guest_rows'] }} data tamu</li>
                        </ul>
                        <label for="reset-confirm" class="form-label mt-3">Ketik <strong>RESET</strong> untuk melanjutkan</label>
                        <input type="text" id="reset-confirm" name="confirm" class="form-control" required
                            pattern="RESET" autocomplete="off" autocapitalize="characters" spellcheck="false"
                            title="Ketik RESET dengan huruf besar">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="reset-submit" disabled>Hapus &amp; Mulai dari Awal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ================= CHECKLIST ================= --}}
    @if ($tab === 'checklist')
        <div class="toolbar">
            <h2>{{ $stats['tasks_done'] }} / {{ $stats['tasks_total'] }} tugas selesai</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTask"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Tugas</button>
        </div>
        @unless ($planner->wedding_date)
            <div class="alert alert-info">Isi tanggal pernikahan di tab <a href="{{ route('client.planner', ['tab' => 'ringkasan']) }}">Ringkasan</a> supaya target tiap tugas terhitung otomatis.</div>
        @endunless

        <div class="phase-list">
            @foreach (Planner::PHASES as $phase => [$label])
                @php $items = $tasks->where('phase', $phase); @endphp
                @continue($items->isEmpty())
                <section class="phase">
                    <header class="phase-head">
                        <h3 class="h5 mb-0">{{ $label }}</h3>
                        <span class="text-muted small tabular">
                            {{ $items->whereNotNull('done_at')->count() }}/{{ $items->count() }}
                            @if ($d = $planner->dueFor($phase)) · {{ $d->translatedFormat('d M Y') }} @endif
                        </span>
                    </header>
                    <div class="panel p-0">
                        @foreach ($items as $t)
                            <div class="task-row {{ $t->done_at ? 'is-done' : '' }}">
                                <form method="POST" action="{{ route('client.planner.tasks.toggle', $t->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="task-check {{ $t->done_at ? 'checked' : '' }}"
                                        aria-label="{{ $t->done_at ? 'Batalkan selesai' : 'Tandai selesai' }}: {{ $t->title }}"
                                        aria-pressed="{{ $t->done_at ? 'true' : 'false' }}">
                                        @if ($t->done_at) <i class="bi bi-check-lg" aria-hidden="true"></i> @endif
                                    </button>
                                </form>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="task-title">{{ $t->title }}</div>
                                    @if ($t->note)
                                        <div class="task-note">{{ $t->note }}</div>
                                    @endif
                                    @if ($t->isOverdue())
                                        <small class="text-danger tabular">Terlambat · {{ $t->due_date->translatedFormat('d M Y') }}</small>
                                    @elseif ($t->done_at)
                                        <small class="text-muted">Selesai {{ $t->done_at->translatedFormat('d M') }}</small>
                                    @endif
                                </div>
                                <div class="task-actions">
                                    <button type="button" class="btn btn-link btn-sm text-muted" data-bs-toggle="modal" data-bs-target="#task{{ $t->id }}"
                                        aria-label="{{ $t->note ? 'Edit' : 'Tambah catatan' }} tugas {{ $t->title }}" title="{{ $t->note ? 'Edit' : 'Tambah catatan' }}">
                                        <i class="bi bi-{{ $t->note ? 'pencil' : 'journal-plus' }}" aria-hidden="true"></i>
                                    </button>
                                    <form method="POST" action="{{ route('client.planner.tasks.destroy', $t->id) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm text-muted" aria-label="Hapus tugas {{ $t->title }}" title="Hapus"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        {{-- Modal tambah ($t = null) & edit tugas --}}
        @foreach ([null, ...$tasks] as $t)
            @php $mid = $t ? 'task' . $t->id : 'modalTask'; @endphp
            <div class="modal fade" id="{{ $mid }}" tabindex="-1" aria-labelledby="{{ $mid }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                    <form method="POST" action="{{ $t ? route('client.planner.tasks.update', $t->id) : route('client.planner.tasks.store') }}" class="modal-content">
                        @csrf @if ($t) @method('PUT') @endif
                        <div class="modal-header">
                            <h2 class="modal-title" id="{{ $mid }}Label">{{ $t ? 'Edit Tugas' : 'Tambah Tugas' }}</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-4">
                                <label for="{{ $mid }}-title" class="form-label">Tugas</label>
                                <input type="text" id="{{ $mid }}-title" name="title" class="form-control" maxlength="150" required value="{{ $t?->title }}">
                            </div>
                            <div class="mb-4">
                                <label for="{{ $mid }}-note" class="form-label">Catatan <span class="optional">(opsional)</span></label>
                                <textarea id="{{ $mid }}-note" name="note" class="form-control" rows="3" maxlength="2000"
                                    placeholder="Contoh: Tanggal akad 17 Agustus 2027, resepsi 18 Agustus" aria-describedby="{{ $mid }}-note-help">{{ $t?->note }}</textarea>
                                <div id="{{ $mid }}-note-help" class="form-text">Simpan detail yang perlu diingat: tanggal, nama vendor, nomor kontak, harga, dsb.</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="{{ $mid }}-phase" class="form-label">Tahap</label>
                                    <select id="{{ $mid }}-phase" name="phase" class="form-select" required>
                                        @foreach (Planner::PHASES as $k => [$label]) <option value="{{ $k }}" @selected($t?->phase === $k)>{{ $label }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="{{ $mid }}-due" class="form-label">Target <span class="optional">(opsional)</span></label>
                                    <input type="date" id="{{ $mid }}-due" name="due_date" class="form-control" value="{{ $t?->due_date?->toDateString() }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">{{ $t ? 'Simpan' : 'Tambah' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ================= ANGGARAN ================= --}}
    @if ($tab === 'anggaran')
        <div class="row stat-grid row-cols-1 row-cols-sm-3">
            <div class="col"><div class="stat-card">
                <span class="stat-label mt-0">Total Anggaran</span>
                <span class="stat-value tabular">{{ $rupiah($planner->budget) }}</span>
            </div></div>
            <div class="col"><div class="stat-card">
                <span class="stat-label mt-0">Rencana Pengeluaran</span>
                <span class="stat-value tabular">{{ $rupiah($stats['estimate']) }}</span>
                @if ($planner->budget > 0)
                    <small class="{{ $sisaAnggaran < 0 ? 'text-danger' : 'text-muted' }} tabular">
                        {{ $sisaAnggaran < 0 ? 'Melebihi ' . $rupiah(-$sisaAnggaran) : 'Sisa ' . $rupiah($sisaAnggaran) }}
                    </small>
                @else
                    <small class="text-muted">Isi total anggaran di Ringkasan</small>
                @endif
            </div></div>
            <div class="col"><div class="stat-card">
                <span class="stat-label mt-0">Sudah Dibayar</span>
                <span class="stat-value tabular">{{ $rupiah($stats['paid']) }}</span>
                <small class="text-muted tabular">Kurang {{ $rupiah($stats['estimate'] - $stats['paid']) }}</small>
            </div></div>
        </div>

        <div class="toolbar">
            <h2>Pos Anggaran</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBudget"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah</button>
        </div>

        @if ($budgetItems->isEmpty())
            <div class="panel text-center">
                <p class="text-muted mx-auto">Belum ada pos anggaran. Mulai dari gedung, catering, atau dekorasi.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBudget">Tambah Pos Pertama</button>
            </div>
        @else
            <div class="table-responsive table-stack-sm">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Kategori</th>
                            <th scope="col">Item</th>
                            <th scope="col" class="text-end">Estimasi</th>
                            <th scope="col" class="text-end">Dibayar</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($budgetItems->sortBy('category') as $b)
                            @php $lunas = $b->paid >= $b->estimate && $b->estimate > 0; @endphp
                            <tr>
                                <td data-label="Kategori" class="text-muted">{{ Planner::BUDGET_CATEGORIES[$b->category] ?? $b->category }}</td>
                                <td data-label="Item">
                                    <span class="fw-semibold">{{ $b->name }}</span>
                                    @if ($b->note) <div class="small text-muted">{{ $b->note }}</div> @endif
                                </td>
                                <td data-label="Estimasi" class="tabular text-end text-nowrap">{{ $rupiah($b->estimate) }}</td>
                                <td data-label="Dibayar" class="tabular text-end text-nowrap">{{ $rupiah($b->paid) }}</td>
                                <td data-label="Status">
                                    <span class="badge bg-{{ $lunas ? 'success' : ($b->paid > 0 ? 'warning' : 'secondary') }}">
                                        {{ $lunas ? 'Lunas' : ($b->paid > 0 ? 'DP' : 'Belum') }}
                                    </span>
                                </td>
                                <td class="col-actions"><div class="table-actions">
                                    <button class="btn btn-outline-secondary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#budget{{ $b->id }}" aria-label="Edit {{ $b->name }}"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                                    <form method="POST" action="{{ route('client.planner.budget.destroy', $b->id) }}" onsubmit="return confirm('Hapus pos ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm btn-icon" aria-label="Hapus {{ $b->name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                    </form>
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @foreach ([null, ...$budgetItems] as $b)
            @php $mid = $b ? 'budget' . $b->id : 'modalBudget'; @endphp
            <div class="modal fade" id="{{ $mid }}" tabindex="-1" aria-labelledby="{{ $mid }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                    <form method="POST" action="{{ $b ? route('client.planner.budget.update', $b->id) : route('client.planner.budget.store') }}" class="modal-content">
                        @csrf @if ($b) @method('PUT') @endif
                        <div class="modal-header">
                            <h2 class="modal-title" id="{{ $mid }}Label">{{ $b ? 'Edit Pos Anggaran' : 'Tambah Pos Anggaran' }}</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-sm-5">
                                    <label for="{{ $mid }}-cat" class="form-label">Kategori</label>
                                    <select id="{{ $mid }}-cat" name="category" class="form-select" required>
                                        @foreach (Planner::BUDGET_CATEGORIES as $k => $v) <option value="{{ $k }}" @selected($b?->category === $k)>{{ $v }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-7">
                                    <label for="{{ $mid }}-name" class="form-label">Item / Vendor</label>
                                    <input type="text" id="{{ $mid }}-name" name="name" class="form-control" maxlength="150" required value="{{ $b?->name }}"
                                        @unless ($b) list="saran-paket" @endunless>
                                </div>
                                <div class="col-6">
                                    <label for="{{ $mid }}-est" class="form-label">Estimasi (Rp)</label>
                                    <input type="number" id="{{ $mid }}-est" name="estimate" class="form-control" min="0" step="1" inputmode="numeric" required value="{{ $b ? (int) $b->estimate : '' }}">
                                </div>
                                <div class="col-6">
                                    <label for="{{ $mid }}-paid" class="form-label">Dibayar (Rp)</label>
                                    <input type="number" id="{{ $mid }}-paid" name="paid" class="form-control" min="0" step="1" inputmode="numeric" value="{{ $b ? (int) $b->paid : '' }}">
                                </div>
                                <div class="col-12">
                                    <label for="{{ $mid }}-note" class="form-label">Catatan <span class="optional">(opsional)</span></label>
                                    <input type="text" id="{{ $mid }}-note" name="note" class="form-control" maxlength="255" value="{{ $b?->note }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
        <datalist id="saran-paket">
            @foreach ($saranPaket as $p)
                <option value="Takari {{ $p->category->nama ?? '' }} {{ ucwords($p->nama) }}">{{ $rupiah($p->harga) }}</option>
            @endforeach
        </datalist>
    @endif

    {{-- ================= TAMU ================= --}}
    @if ($tab === 'tamu')
        <div class="row stat-grid row-cols-2 row-cols-lg-4">
            @foreach ([
                ['Diundang', $stats['guest_pax'], $planner->guest_target ? 'target ' . $planner->guest_target : $stats['guest_rows'] . ' undangan'],
                ['Hadir', $stats['guest_hadir'], 'orang'],
                ['Tidak Hadir', $stats['guest_tidak'], 'orang'],
                ['Belum Konfirmasi', $stats['guest_pax'] - $stats['guest_hadir'] - $stats['guest_tidak'], 'orang'],
            ] as [$label, $val, $sub])
                <div class="col"><div class="stat-card">
                    <span class="stat-label mt-0">{{ $label }}</span>
                    <span class="stat-value">{{ $val }}</span>
                    <small class="text-muted">{{ $sub }}</small>
                </div></div>
            @endforeach
        </div>

        <div class="toolbar">
            <form method="GET" class="guest-search" role="search">
                <input type="hidden" name="tab" value="tamu">
                <label for="guest-filter" class="visually-hidden">Cari tamu</label>
                <input type="search" id="guest-filter" class="form-control" placeholder="Cari nama tamu…" data-filter-target="#guest-table tbody tr">
            </form>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalGuest"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Tamu</button>
        </div>

        @if ($guests->isEmpty())
            <div class="panel text-center">
                <p class="text-muted mx-auto">Belum ada tamu. Tambahkan keluarga dan sahabat terdekat dulu.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGuest">Tambah Tamu Pertama</button>
            </div>
        @else
            <div class="table-responsive table-stack-sm">
                <table class="table table-hover align-middle" id="guest-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Pihak</th>
                            <th scope="col">No. HP</th>
                            <th scope="col" class="text-end">Jumlah</th>
                            <th scope="col">Kehadiran</th>
                            <th scope="col" class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guests as $g)
                            <tr>
                                <td data-label="Nama" class="fw-semibold">{{ $g->name }}</td>
                                <td data-label="Pihak" class="text-muted">{{ Planner::SIDES[$g->side] ?? $g->side }}</td>
                                <td data-label="No. HP" class="tabular">
                                    @if ($wa = \App\Support\Phone::waLink($g->phone))
                                        <a href="{{ $wa }}" target="_blank" rel="noopener">{{ $g->phone }}</a>
                                    @else
                                        {{ $g->phone ?: '—' }}
                                    @endif
                                </td>
                                <td data-label="Jumlah" class="tabular text-end">{{ $g->pax }}</td>
                                <td data-label="Kehadiran">
                                    <form method="POST" action="{{ route('client.planner.guests.rsvp', $g->id) }}">
                                        @csrf @method('PATCH')
                                        <label for="rsvp-{{ $g->id }}" class="visually-hidden">Kehadiran {{ $g->name }}</label>
                                        <select id="rsvp-{{ $g->id }}" name="rsvp" class="form-select form-select-sm rsvp-select rsvp-{{ $g->rsvp }}" onchange="this.form.submit()">
                                            @foreach (Planner::RSVP as $k => $v) <option value="{{ $k }}" @selected($g->rsvp === $k)>{{ $v }}</option> @endforeach
                                        </select>
                                        <noscript><button class="btn btn-sm btn-outline-secondary mt-1">Simpan</button></noscript>
                                    </form>
                                </td>
                                <td class="col-actions"><div class="table-actions">
                                    <button class="btn btn-outline-secondary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#guest{{ $g->id }}" aria-label="Edit {{ $g->name }}"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                                    <form method="POST" action="{{ route('client.planner.guests.destroy', $g->id) }}" onsubmit="return confirm('Hapus tamu ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm btn-icon" aria-label="Hapus {{ $g->name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                    </form>
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-3" id="guest-empty" hidden>Tidak ada tamu yang cocok.</p>
        @endif

        @foreach ([null, ...$guests] as $g)
            @php $mid = $g ? 'guest' . $g->id : 'modalGuest'; @endphp
            <div class="modal fade" id="{{ $mid }}" tabindex="-1" aria-labelledby="{{ $mid }}Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                    <form method="POST" action="{{ $g ? route('client.planner.guests.update', $g->id) : route('client.planner.guests.store') }}" class="modal-content">
                        @csrf @if ($g) @method('PUT') @endif
                        <div class="modal-header">
                            <h2 class="modal-title" id="{{ $mid }}Label">{{ $g ? 'Edit Tamu' : 'Tambah Tamu' }}</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="{{ $mid }}-name" class="form-label">Nama</label>
                                    <input type="text" id="{{ $mid }}-name" name="name" class="form-control" maxlength="120" required value="{{ $g?->name }}">
                                </div>
                                <div class="col-sm-6">
                                    <label for="{{ $mid }}-side" class="form-label">Pihak</label>
                                    <select id="{{ $mid }}-side" name="side" class="form-select" required>
                                        @foreach (Planner::SIDES as $k => $v) <option value="{{ $k }}" @selected($g?->side === $k)>{{ $v }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="{{ $mid }}-pax" class="form-label">Jumlah Orang</label>
                                    <input type="number" id="{{ $mid }}-pax" name="pax" class="form-control" min="1" max="20" required value="{{ $g?->pax ?? 1 }}">
                                </div>
                                <div class="col-sm-6">
                                    <label for="{{ $mid }}-phone" class="form-label">No. HP <span class="optional">(opsional)</span></label>
                                    <input type="tel" id="{{ $mid }}-phone" name="phone" class="form-control" maxlength="30" value="{{ $g?->phone }}">
                                </div>
                                <div class="col-sm-6">
                                    <label for="{{ $mid }}-rsvp" class="form-label">Kehadiran</label>
                                    <select id="{{ $mid }}-rsvp" name="rsvp" class="form-select">
                                        @foreach (Planner::RSVP as $k => $v) <option value="{{ $k }}" @selected(($g?->rsvp ?? 'pending') === $k)>{{ $v }}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Tombol reset aktif hanya bila teks konfirmasi tepat "RESET" (server tetap memvalidasi)
    document.addEventListener('input', e => {
        if (e.target.id !== 'reset-confirm') return;
        document.getElementById('reset-submit').disabled = e.target.value !== 'RESET';
    });

    // Filter tamu di sisi browser (daftar sudah lengkap di halaman)
    document.addEventListener('input', e => {
        const input = e.target.closest('[data-filter-target]');
        if (!input) return;
        const q = input.value.trim().toLowerCase();
        let shown = 0;
        document.querySelectorAll(input.dataset.filterTarget).forEach(row => {
            row.hidden = q && !row.textContent.toLowerCase().includes(q);
            if (!row.hidden) shown++;
        });
        const empty = document.getElementById('guest-empty');
        if (empty) empty.hidden = shown > 0;
    });
</script>
@endpush
