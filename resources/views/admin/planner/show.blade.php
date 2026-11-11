@extends('layouts.main')

@section('title', 'Planner ' . ($planner->client->name ?? '') . ' | Admin Takari')

@php
    use App\Models\Planner;
    $rupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $pct = fn ($a, $b) => $b > 0 ? min(100, (int) round($a / $b * 100)) : 0;
    $c = $planner->client;
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main planner">
            <a href="{{ route('admin.planners.index') }}" class="btn btn-link btn-sm mb-3"><i class="bi bi-arrow-left" aria-hidden="true"></i> Semua Planner</a>

            <header class="page-header">
                <div>
                    <span class="eyebrow">Planner · {{ $c->name ?? '—' }}</span>
                    <h1 class="page-title">
                        @if ($planner->partner_1 || $planner->partner_2)
                            {{ $planner->partner_1 ?: '…' }} <span class="amp">&amp;</span> {{ $planner->partner_2 ?: '…' }}
                        @else
                            Rencana Pernikahan
                        @endif
                    </h1>
                </div>
                @if ($stats['days_left'] !== null && $stats['days_left'] >= 0)
                    <div class="countdown">
                        <span class="countdown-num">{{ $stats['days_left'] }}</span>
                        <span class="countdown-label">hari lagi<br>{{ $planner->wedding_date->translatedFormat('d M Y') }}</span>
                    </div>
                @endif
            </header>

            <div class="row gy-4 gx-lg-5 mb-5">
                <div class="col-lg-5">
                    <div class="panel h-100">
                        <h2 class="h5 mb-4">Kontak Client</h2>
                        <dl class="info-list">
                            <dt>Nama</dt><dd>{{ $c->name ?? '—' }}</dd>
                            <dt>Username</dt><dd>{{ $c->username ?? '—' }}</dd>
                            <dt>Email</dt><dd>@if ($c?->email)<a href="mailto:{{ $c->email }}">{{ $c->email }}</a>@else — @endif</dd>
                            <dt>No. HP</dt>
                            <dd>
                                @if ($wa = \App\Support\Phone::waLink($c?->phone))
                                    <a href="{{ $wa }}" target="_blank" rel="noopener">{{ $c->phone }} <i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                                @else
                                    {{ $c?->phone ?: '—' }}
                                @endif
                            </dd>
                            <dt>Lokasi</dt><dd>{{ $planner->venue ?: '—' }}</dd>
                            <dt>Dibuat</dt><dd class="tabular">{{ $planner->created_at->translatedFormat('d M Y') }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row row-cols-2 g-3 h-100">
                        <div class="col"><div class="stat-card">
                            <span class="stat-label mt-0">Checklist</span>
                            <span class="stat-value">{{ $pct($stats['tasks_done'], $stats['tasks_total']) }}%</span>
                            <div class="progress-line"><span style="--v: {{ $pct($stats['tasks_done'], $stats['tasks_total']) }}%"></span></div>
                            <small class="text-muted">{{ $stats['tasks_done'] }}/{{ $stats['tasks_total'] }} · {{ $stats['overdue'] }} terlambat</small>
                        </div></div>
                        <div class="col"><div class="stat-card">
                            <span class="stat-label mt-0">Anggaran</span>
                            <span class="stat-value stat-value-sm tabular">{{ $rupiah($planner->budget) }}</span>
                            <small class="text-muted tabular">rencana {{ $rupiah($stats['estimate']) }}</small>
                        </div></div>
                        <div class="col"><div class="stat-card">
                            <span class="stat-label mt-0">Terbayar</span>
                            <span class="stat-value">{{ $pct($stats['paid'], $stats['estimate']) }}%</span>
                            <div class="progress-line"><span style="--v: {{ $pct($stats['paid'], $stats['estimate']) }}%"></span></div>
                            <small class="text-muted tabular">{{ $rupiah($stats['paid']) }}</small>
                        </div></div>
                        <div class="col"><div class="stat-card">
                            <span class="stat-label mt-0">Tamu</span>
                            <span class="stat-value">{{ $stats['guest_pax'] }}</span>
                            <small class="text-muted">{{ $stats['guest_hadir'] }} hadir{{ $planner->guest_target ? ' · target ' . $planner->guest_target : '' }}</small>
                        </div></div>
                    </div>
                </div>
            </div>

            {{-- Anggaran: paling berguna untuk menawarkan paket --}}
            <div class="toolbar"><h2>Pos Anggaran</h2></div>
            <div class="table-responsive table-stack-sm mb-5">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Kategori</th>
                            <th scope="col">Item</th>
                            <th scope="col" class="text-end">Estimasi</th>
                            <th scope="col" class="text-end">Dibayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($planner->budgetItems->sortBy('category') as $b)
                            <tr>
                                <td data-label="Kategori" class="text-muted">{{ Planner::BUDGET_CATEGORIES[$b->category] ?? $b->category }}</td>
                                <td data-label="Item">
                                    <span class="fw-semibold">{{ $b->name }}</span>
                                    @if ($b->note) <div class="small text-muted">{{ $b->note }}</div> @endif
                                </td>
                                <td data-label="Estimasi" class="tabular text-end text-nowrap">{{ $rupiah($b->estimate) }}</td>
                                <td data-label="Dibayar" class="tabular text-end text-nowrap">{{ $rupiah($b->paid) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pos anggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row gy-5 gx-lg-5">
                <div class="col-xl-7">
                    <div class="toolbar"><h2>Checklist</h2></div>
                    <div class="phase-list">
                        @foreach (Planner::PHASES as $phase => [$label])
                            @php $items = $planner->tasks->where('phase', $phase); @endphp
                            @continue($items->isEmpty())
                            <section>
                                <header class="phase-head">
                                    <h3 class="h6 mb-0">{{ $label }}</h3>
                                    <span class="text-muted small tabular">{{ $items->whereNotNull('done_at')->count() }}/{{ $items->count() }}</span>
                                </header>
                                <div class="panel p-0">
                                    @foreach ($items as $t)
                                        <div class="task-row {{ $t->done_at ? 'is-done' : '' }}">
                                            <span class="task-check {{ $t->done_at ? 'checked' : '' }}" aria-hidden="true">
                                                @if ($t->done_at) <i class="bi bi-check-lg"></i> @endif
                                            </span>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="task-title">
                                                    {{ $t->title }}
                                                    <span class="visually-hidden">{{ $t->done_at ? '(selesai)' : '(belum)' }}</span>
                                                </div>
                                                @if ($t->note) <div class="task-note">{{ $t->note }}</div> @endif
                                                @if ($t->isOverdue())
                                                    <small class="text-danger tabular">Terlambat · {{ $t->due_date->translatedFormat('d M Y') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="toolbar"><h2>Tamu</h2></div>
                    <div class="table-responsive table-stack-sm">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Pihak</th>
                                    <th scope="col" class="text-end">Jml</th>
                                    <th scope="col">Hadir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($planner->guests as $g)
                                    @php $badge = ['hadir' => 'success', 'tidak' => 'danger'][$g->rsvp] ?? 'warning'; @endphp
                                    <tr>
                                        <td data-label="Nama" class="fw-semibold">{{ $g->name }}</td>
                                        <td data-label="Pihak" class="text-muted">{{ Planner::SIDES[$g->side] ?? $g->side }}</td>
                                        <td data-label="Jumlah" class="tabular text-end">{{ $g->pax }}</td>
                                        <td data-label="Kehadiran"><span class="badge bg-{{ $badge }}">{{ Planner::RSVP[$g->rsvp] ?? $g->rsvp }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada tamu.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
