@extends('layouts.main')

@section('title', 'Takari Wedding Organizer')

@section('content')

    @if ($showWelcome)
        <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-body py-5">
                        <span class="eyebrow">Selamat Datang</span>
                        <h2 class="mb-4" id="welcomeModalLabel">Halo, {{ $client_name }}</h2>
                        <p class="text-muted mx-auto">Senang kamu bergabung. Silakan jelajahi paket kami dan pilih yang paling sesuai.</p>
                        <button class="btn btn-primary mt-4" data-bs-dismiss="modal">Lihat Katalog</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Hero --}}
    <section class="hero">
        <picture>
            <source type="image/webp" srcset="{{ asset('asset/img/img-1-sm.webp') }} 800w, {{ asset('asset/img/img-1.webp') }} 1200w" sizes="100vw">
            <img src="{{ asset('asset/img/img-1.jpg') }}" alt="" fetchpriority="high" decoding="async" width="1200" height="699">
        </picture>
        <div class="container hero-content">
            <span class="eyebrow">Takari Wedding Organizer</span>
            <h1>Make dreams come true with us</h1>
            <p>Dekorasi, gedung, dan setiap detail hari bahagiamu, ditangani dengan tenang dan sepenuh hati.</p>
            <a href="#katalog" class="btn btn-light">Lihat Paket</a>
        </div>
    </section>

    {{-- Tentang --}}
    <section id="about" class="section">
        <div class="container">
            <div class="row gy-5 gx-lg-5 align-items-center">
                <div class="col-lg-5 reveal">
                    <span class="eyebrow">Tentang Kami</span>
                    <h2 class="section-title">Momen penuh makna, tanpa beban</h2>
                    <p class="lead mt-5">
                        TAKARI Wedding Organizer hadir untuk mewujudkan hari spesialmu menjadi momen penuh kenangan.
                        Dengan pengalaman profesional, kami mendampingi pasangan mulai dari dekorasi elegan,
                        catering, dokumentasi, hingga hiburan.
                    </p>
                    <p class="text-muted mb-0">Serahkan detailnya pada kami, dan nikmati kebahagiaanmu.</p>
                </div>
                <div class="col-lg-7 reveal">
                    <div class="row gallery">
                        @foreach (['img-2.jpg' => 'Dekorasi pelaminan', 'img-3.jpg' => 'Suasana resepsi'] as $img => $alt)
                            <div class="col-6 {{ $loop->first ? '' : 'offset-down' }}">
                                @php $base = asset('asset/img/' . pathinfo($img, PATHINFO_FILENAME)); @endphp
                                <picture>
                                    <source type="image/webp" srcset="{{ $base }}-sm.webp 800w, {{ $base }}.webp 1200w" sizes="(min-width: 1200px) 340px, (min-width: 992px) 28vw, 50vw">
                                    <img src="{{ asset('asset/img/' . $img) }}" alt="{{ $alt }} Takari" loading="lazy" decoding="async" width="1200" height="800">
                                </picture>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Katalog --}}
    @php
        $semuaPaket = $kategoriList->flatMap(fn ($k) => $k->packages->map(fn ($p) => $p->setRelation('category', $k)));
        $hargaMulai = $semuaPaket->min('harga');
        $unggulan = $kategoriList->mapWithKeys(fn ($k) => [$k->id => $k->packages->sortByDesc('harga')->first()?->id]);
        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    @endphp
    <section id="katalog" class="section bg-sand">
        <div class="container">
            <div class="section-header text-center">
                <span class="eyebrow">Katalog</span>
                <h2 class="section-title">Paket Pernikahan</h2>
                @if ($semuaPaket->isNotEmpty())
                    <p class="text-muted mt-5 mb-0">
                        {{ $semuaPaket->count() }} pilihan paket dalam {{ $kategoriList->count() }} kategori,
                        mulai dari <span class="price">{{ $rupiah($hargaMulai) }}</span>.
                    </p>
                @endif
            </div>

            @if ($kategoriList->count() > 1)
                <div class="category-filter" role="group" aria-label="Filter kategori">
                    <button type="button" data-filter="all" aria-pressed="true">Semua <span class="count">{{ $semuaPaket->count() }}</span></button>
                    @foreach ($kategoriList as $kategori)
                        <button type="button" data-filter="{{ $kategori->id }}" aria-pressed="false">
                            {{ $kategori->nama }} <span class="count">{{ $kategori->packages->count() }}</span>
                        </button>
                    @endforeach
                </div>
                <p id="katalog-count" class="visually-hidden" aria-live="polite">{{ $semuaPaket->count() }} paket</p>
            @endif

            <div class="row package-grid">
                @forelse ($semuaPaket as $row)
                    @php
                        $rincian = $row->rincian;
                        $isUnggulan = $unggulan[$row->category_id] === $row->id && $row->category->packages->count() > 1;
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3" data-category="{{ $row->category_id }}">
                        <article class="package-card">
                            <a href="#detail{{ $row->id }}" class="media" data-bs-toggle="modal" data-bs-target="#detail{{ $row->id }}"
                                aria-label="Lihat detail paket {{ $row->nama }}">
                                <img src="{{ $row->gambar_kecil_url }}" srcset="{{ $row->gambar_kecil_url }} 640w, {{ $row->gambar_url }} 1200w"
                                    sizes="(min-width: 1200px) 280px, (min-width: 992px) 30vw, (min-width: 576px) 50vw, 100vw"
                                    alt="Paket {{ $row->nama }} — {{ $row->category->nama }}" loading="lazy" decoding="async" width="640" height="480">
                                <span class="media-tag">{{ $row->category->nama }}</span>
                                @if ($isUnggulan)
                                    <span class="media-flag">Terlengkap</span>
                                @endif
                                @if ($row->video)
                                    <span class="media-play" aria-hidden="true"><i class="bi bi-play-fill"></i></span>
                                @endif
                            </a>
                            <div class="body">
                                <h3>{{ ucwords($row->nama) }}</h3>
                                @if ($rincian['ringkasan'])
                                    <p class="summary">{{ $rincian['ringkasan'] }}</p>
                                @endif
                                @if ($rincian['fitur'])
                                    <ul class="features">
                                        @foreach (array_slice($rincian['fitur'], 0, 3) as $f)
                                            <li>{{ $f }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="actions">
                                    <div>
                                        <span class="price-label">Harga</span>
                                        <span class="price">{{ $rupiah($row->harga) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#detail{{ $row->id }}">
                                        Detail
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    @push('modals')
                        <div class="modal fade" id="detail{{ $row->id }}" tabindex="-1" aria-labelledby="detailLabel{{ $row->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                <div class="modal-content package-detail">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    <div class="row g-0">
                                        <div class="col-md-5 detail-media">
                                            @if ($row->video_url)
                                                <video src="{{ $row->video_url }}" poster="{{ $row->gambar_url }}" controls playsinline preload="none"
                                                    aria-label="Video paket {{ $row->nama }}"></video>
                                            @else
                                                <img src="{{ $row->gambar_url }}" alt="" loading="lazy" decoding="async">
                                            @endif
                                        </div>
                                        <div class="col-md-7 detail-body">
                                            <span class="eyebrow">{{ $row->category->nama }}</span>
                                            <h2 class="modal-title" id="detailLabel{{ $row->id }}">Paket {{ ucwords($row->nama) }}</h2>
                                            <div class="price price-lg mt-2 mb-4">{{ $rupiah($row->harga) }}</div>
                                            @if ($rincian['ringkasan'])
                                                <p class="text-muted">{{ $rincian['ringkasan'] }}</p>
                                            @endif
                                            @if ($rincian['fitur'])
                                                <span class="eyebrow mt-4 mb-3">Termasuk</span>
                                                <ul class="features features-full">
                                                    @foreach ($rincian['fitur'] as $f)
                                                        <li>{{ $f }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            <a href="{{ route('client.payments.show', $row->id) }}" class="btn btn-primary w-100 mt-4">Pesan Paket Ini</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endpush
                @empty
                    <p class="text-center text-muted mx-auto">Paket akan segera hadir.</p>
                @endforelse
            </div>
        </div>
    </section>

    @stack('modals')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if ($showWelcome)
            new bootstrap.Modal(document.getElementById('welcomeModal')).show();
        @endif

        const buttons = document.querySelectorAll('.category-filter button');
        const cards = document.querySelectorAll('[data-category]');
        const count = document.getElementById('katalog-count');
        buttons.forEach(btn => btn.addEventListener('click', () => {
            buttons.forEach(b => b.setAttribute('aria-pressed', b === btn));
            let shown = 0;
            cards.forEach(card => {
                card.hidden = btn.dataset.filter !== 'all' && card.dataset.category !== btn.dataset.filter;
                if (!card.hidden) shown++;
            });
            if (count) count.textContent = shown + ' paket ditampilkan';
        }));
    });
</script>
@endpush
