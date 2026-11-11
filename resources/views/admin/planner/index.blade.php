@extends('layouts.main')

@section('title', 'Wedding Planner | Admin Takari')

@php
    $rupiah = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main">
            <header class="page-header">
                <div>
                    <span class="eyebrow">Client</span>
                    <h1 class="page-title">Wedding Planner</h1>
                </div>
            </header>

            <div class="row row-cols-1 row-cols-sm-3 stat-grid">
                <div class="col"><div class="stat-card">
                    <i class="bi bi-clipboard-heart" aria-hidden="true"></i>
                    <span class="stat-label">Total Planner</span>
                    <span class="stat-value">{{ $summary['total'] }}</span>
                </div></div>
                <div class="col"><div class="stat-card">
                    <i class="bi bi-calendar-heart" aria-hidden="true"></i>
                    <span class="stat-label">Menikah ≤ 3 Bulan</span>
                    <span class="stat-value">{{ $summary['upcoming'] }}</span>
                </div></div>
                <div class="col"><div class="stat-card">
                    <i class="bi bi-calendar-x" aria-hidden="true"></i>
                    <span class="stat-label">Belum Isi Tanggal</span>
                    <span class="stat-value">{{ $summary['no_date'] }}</span>
                </div></div>
            </div>

            <div class="toolbar">
                <h2>Daftar Planner</h2>
                <form method="GET" class="guest-search" role="search">
                    <label for="planner-q" class="visually-hidden">Cari planner</label>
                    <input type="search" id="planner-q" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama pasangan / client…">
                </form>
            </div>

            <div class="table-responsive table-stack-sm">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Client</th>
                            <th scope="col">Pasangan</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Checklist</th>
                            <th scope="col" class="text-end">Anggaran</th>
                            <th scope="col" class="text-end">Tamu</th>
                            <th scope="col" class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($planners as $p)
                            @php
                                $pct = $p->tasks_count ? (int) round($p->tasks_done_count / $p->tasks_count * 100) : 0;
                                $days = $p->daysLeft();
                            @endphp
                            <tr>
                                <td data-label="Client">
                                    <div class="fw-semibold">{{ $p->client->name ?? '—' }}</div>
                                    <small class="text-muted">{{ '@' . ($p->client->username ?? '') }}</small>
                                </td>
                                <td data-label="Pasangan">
                                    @if ($p->partner_1 || $p->partner_2)
                                        {{ $p->partner_1 ?: '…' }} &amp; {{ $p->partner_2 ?: '…' }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Tanggal" class="text-nowrap">
                                    @if ($p->wedding_date)
                                        <div class="tabular">{{ $p->wedding_date->translatedFormat('d M Y') }}</div>
                                        <small class="{{ $days !== null && $days >= 0 && $days <= 90 ? 'text-rose fw-semibold' : 'text-muted' }}">
                                            {{ $days > 0 ? $days . ' hari lagi' : ($days === 0 ? 'Hari ini' : 'Sudah lewat') }}
                                        </small>
                                    @else
                                        <span class="text-muted">Belum diisi</span>
                                    @endif
                                </td>
                                <td data-label="Checklist" class="col-progress">
                                    <div class="tabular small">{{ $p->tasks_done_count }}/{{ $p->tasks_count }} · {{ $pct }}%</div>
                                    <div class="progress-line mt-1"><span style="--v: {{ $pct }}%"></span></div>
                                </td>
                                <td data-label="Anggaran" class="tabular text-end text-nowrap">
                                    {{ $rupiah($p->budget) }}
                                    @if ($p->estimate_sum)
                                        <div class="small text-muted">rencana {{ $rupiah($p->estimate_sum) }}</div>
                                    @endif
                                </td>
                                <td data-label="Tamu" class="tabular text-end">{{ (int) $p->guest_pax_sum }}</td>
                                <td class="col-actions"><div class="table-actions">
                                    @if ($wa = \App\Support\Phone::waLink($p->client?->phone))
                                        <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm btn-icon"
                                            aria-label="WhatsApp {{ $p->client->name }}" title="WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                                    @endif
                                    <a href="{{ route('admin.planners.show', $p->id) }}" class="btn btn-outline-secondary btn-sm">Detail</a>
                                </div></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    {{ $q ? 'Tidak ada planner yang cocok.' : 'Belum ada client yang membuat wedding planner.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $planners->links() }}
        </div>
    </div>
</div>
@endsection
