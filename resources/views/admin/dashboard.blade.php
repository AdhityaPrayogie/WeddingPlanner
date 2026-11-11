@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main">
            <header class="page-header">
                <div>
                    <span class="eyebrow">Ringkasan</span>
                    <h1 class="page-title">Dashboard</h1>
                </div>
            </header>
            <div class="row row-cols-2 row-cols-xl-4 stat-grid">
                <div class="col">
                    <div class="stat-card">
                        <i class="bi bi-graph-up" aria-hidden="true"></i>
                        <span class="stat-label">Total</span>
                        <span class="stat-value">{{ $totalData }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-card">
                        <i class="bi bi-check-circle" aria-hidden="true"></i>
                        <span class="stat-label">Dikonfirmasi</span>
                        <span class="stat-value">{{ $confirmed }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-card">
                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                        <span class="stat-label">Menunggu</span>
                        <span class="stat-value">{{ $pending }}</span>
                    </div>
                </div>
                <div class="col">
                    <div class="stat-card">
                        <i class="bi bi-x-circle" aria-hidden="true"></i>
                        <span class="stat-label">Dibatalkan</span>
                        <span class="stat-value">{{ $cancelled }}</span>
                    </div>
                </div>
            </div>
            <div class="toolbar">
                <h2>Daftar Pemesanan</h2>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                        id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Sortir
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['sort' => 'created_at']) }}">Berdasarkan Waktu</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['sort' => 'client']) }}">Berdasarkan Client</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['sort' => 'paket']) }}">Berdasarkan Nama Paket</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['sort' => 'kategori']) }}">Berdasarkan Kategori</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive table-stack-sm table-stack-lg">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Client</th>
                            <th scope="col">Tanggal Acara</th>
                            <th scope="col">Paket</th>
                            <th scope="col" class="text-end">Harga</th>
                            <th scope="col" class="col-optional">Catatan</th>
                            <th scope="col">Bukti</th>
                            <th scope="col" class="col-optional">Dipesan</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $row)
                            @php
                                $badge = ['confirmed' => 'success', 'pending' => 'warning', 'cancelled' => 'danger'][$row->status] ?? 'secondary';
                                $label = ['confirmed' => 'Dikonfirmasi', 'pending' => 'Menunggu', 'cancelled' => 'Dibatalkan'][$row->status] ?? $row->status;
                            @endphp
                            <tr>
                                <td data-label="Client" class="fw-semibold">{{ $row->client->name ?? '—' }}</td>
                                <td data-label="Tanggal" class="tabular text-nowrap">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td data-label="Paket" class="text-nowrap">{{ ucwords($row->paket) }}</td>
                                <td data-label="Harga" class="tabular text-nowrap text-end">{{ $row->harga ? 'Rp ' . number_format($row->harga, 0, ',', '.') : '—' }}</td>
                                <td data-label="Catatan" class="text-muted col-optional"><div class="cell-clamp">{{ $row->catatan ?: '—' }}</div></td>
                                <td data-label="Bukti">
                                    @if ($row->bukti_pembayaran)
                                        <a href="{{ route('admin.orders.bukti', $row->id) }}" target="_blank" rel="noopener">
                                            <img src="{{ route('admin.orders.bukti', $row->id) }}" class="thumb" loading="lazy" decoding="async" width="56" height="56" alt="Bukti pembayaran {{ $row->client->name ?? '' }}">
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td data-label="Dipesan" class="tabular text-muted text-nowrap col-optional">{{ $row->created_at->format('d M Y, H:i') }}</td>
                                <td data-label="Status"><span class="badge bg-{{ $badge }}">{{ $label }}</span></td>
                                <td class="col-actions">
                                    <div class="table-actions">
                                        <form action="{{ route('admin.orders.update', $row->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="action" value="confirmed" class="btn btn-sm btn-icon btn-success" aria-label="Konfirmasi" title="Konfirmasi" @disabled($row->status === 'confirmed')>
                                                <i class="bi bi-check-lg" aria-hidden="true"></i>
                                            </button>
                                            <button type="submit" name="action" value="pending" class="btn btn-sm btn-icon btn-warning" aria-label="Tandai menunggu" title="Menunggu" @disabled($row->status === 'pending')>
                                                <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                                            </button>
                                            <button type="submit" name="action" value="cancelled" class="btn btn-sm btn-icon btn-danger" aria-label="Batalkan" title="Batalkan" @disabled($row->status === 'cancelled')>
                                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.orders.destroy', ['id' => $row->id]) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" aria-label="Hapus pesanan" title="Hapus">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}

        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: center;
        border-radius: 12px;
        padding: 20px;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    table th, table td {
        vertical-align: middle !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection
