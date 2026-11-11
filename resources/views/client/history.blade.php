@extends('layouts.main')

@section('title', 'Riwayat Pesanan | Takari')

@section('content')
<div class="container section">
    <header class="page-header">
        <div>
            <span class="eyebrow">Akun</span>
            <h1 class="page-title">Riwayat Pesanan</h1>
        </div>
        @if ($orders->isNotEmpty())
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer" aria-hidden="true"></i> Cetak
            </button>
        @endif
    </header>

    @if ($orders->isNotEmpty())
        <div class="table-responsive table-stack-sm">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Tanggal Acara</th>
                        <th scope="col">Paket</th>
                        <th scope="col" class="text-end">Harga</th>
                        <th scope="col">Catatan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Bukti</th>
                        <th scope="col">Dipesan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td data-label="Tanggal Acara" class="tabular text-nowrap">{{ \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d M Y') }}</td>
                            <td data-label="Paket" class="fw-semibold">{{ $order->paket }}</td>
                            <td data-label="Harga" class="tabular text-end text-nowrap">{{ $order->harga ? 'Rp ' . number_format($order->harga, 0, ',', '.') : '—' }}</td>
                            <td data-label="Catatan" class="text-muted"><div class="cell-clamp">{{ $order->catatan ?: '—' }}</div></td>
                            <td data-label="Status">
                                @php
                                    $badge = ['confirmed' => 'success', 'pending' => 'warning', 'cancelled' => 'danger'][$order->status] ?? 'secondary';
                                    $label = ['confirmed' => 'Dikonfirmasi', 'pending' => 'Menunggu', 'cancelled' => 'Dibatalkan'][$order->status] ?? $order->status;
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                            </td>
                            <td data-label="Bukti">
                                @if ($order->bukti_pembayaran)
                                    <a href="{{ route('client.orders.bukti', $order->id) }}" target="_blank" rel="noopener">
                                        <img src="{{ route('client.orders.bukti', $order->id) }}" class="thumb" width="56" height="56" alt="Bukti pembayaran {{ $order->paket }}" loading="lazy">
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td data-label="Dipesan" class="tabular text-muted text-nowrap">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="panel text-center">
            <p class="text-muted mx-auto mb-4">Belum ada pesanan.</p>
            <a href="{{ route('home') }}#katalog" class="btn btn-primary">Lihat Katalog</a>
        </div>
    @endif
</div>
@endsection
