@extends('layouts.main')

@section('title', 'Pesan ' . $paket->nama . ' | Takari')

@section('content')
<div class="container section">
    @if (session('sukses'))
        <div class="page-narrow text-center">
            <i class="bi bi-check2-circle text-success icon-xl" aria-hidden="true"></i>
            <span class="eyebrow">Pesanan Terkirim</span>
            <h1 class="mb-4">Terima kasih</h1>
            <p class="text-muted mx-auto">{{ session('sukses') }} Kami akan memverifikasi pembayaranmu secepatnya.</p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-5">
                <a href="{{ route('client.orders.history') }}" class="btn btn-primary">Lihat Pesanan</a>
                <a href="{{ route('home') }}#katalog" class="btn btn-outline-secondary">Kembali ke Katalog</a>
            </div>
        </div>
    @else
        <div class="row gy-5 gx-lg-5">
            {{-- Ringkasan paket --}}
            <div class="col-lg-5">
                <article class="package-card">
                    <div class="media">
                        <img src="{{ $paket->gambar_url }}" alt="Paket {{ $paket->nama }}" width="400" height="300" fetchpriority="high">
                    </div>
                    <div class="body">
                        <span class="eyebrow">{{ $paket->category->nama ?? 'Paket' }}</span>
                        <h1 class="h3">{{ $paket->nama }}</h1>
                        <div class="price price-lg mb-4">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>
                        <p class="text-muted mb-0">{!! nl2br(e($paket->deskripsi)) !!}</p>
                    </div>
                </article>
            </div>

            {{-- Form --}}
            <div class="col-lg-7">
                <header class="page-header">
                    <div>
                        <span class="eyebrow">Pemesanan</span>
                        <h2 class="page-title">Lengkapi detail acara</h2>
                    </div>
                </header>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('client.payments.process', ['package' => $paket->id]) }}" enctype="multipart/form-data" class="panel">
                    @csrf

                    <div class="mb-4">
                        <label for="tanggal" class="form-label">Tanggal Acara</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required
                            min="{{ now()->toDateString() }}" value="{{ old('tanggal') }}">
                    </div>

                    <div class="mb-4">
                        <label for="catatan" class="form-label">Catatan <span class="optional">(opsional)</span></label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" maxlength="1000">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="note mb-4">
                        <div class="eyebrow mb-2">Transfer ke</div>
                        <div class="tabular">BCA <strong>1234567890</strong></div>
                        <div class="tabular">DANA <strong>0812xxxxxxx</strong></div>
                    </div>

                    <div class="mb-4">
                        <label for="bukti" class="form-label">Bukti Pembayaran</label>
                        <input type="file" name="bukti" id="bukti" class="form-control" required accept="image/jpeg,image/png,image/webp" data-max-mb="5" aria-describedby="bukti-help">
                        <div id="bukti-help" class="form-text">JPG, PNG, atau WebP. Maks. 5 MB.</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-5">
                        <button type="submit" class="btn btn-primary">Konfirmasi Pesanan</button>
                        <a href="{{ route('home') }}#katalog" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
