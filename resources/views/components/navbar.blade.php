@php
    // Anchor home harus tetap jalan dari halaman lain
    $home = Request::routeIs('home') ? '' : route('home');
@endphp
<nav class="navbar navbar-expand-lg sticky-top site-nav" aria-label="Navigasi utama">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('asset/img/Takari.png') }}" alt="Takari Wedding Organizer" height="40">
        </a>

        <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse d-none d-lg-flex">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $home }}#about">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ $home }}#katalog">Katalog</a></li>
                <li class="nav-item"><a class="nav-link {{ Request::routeIs('client.planner*') ? 'active' : '' }}" href="{{ route('client.planner') }}">Wedding Planner</a></li>
            </ul>

            @auth('client')
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person" aria-hidden="true"></i> {{ Auth::guard('client')->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        <li><a class="dropdown-item" href="{{ route('client.planner') }}">Wedding Planner</a></li>
                        <li><a class="dropdown-item" href="{{ route('client.profile.index') }}">Profil Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('client.orders.history') }}">Riwayat Pesanan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('client.logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <div class="d-flex align-items-center gap-4">
                    <a class="btn btn-link btn-sm" href="{{ route('client.login') }}">Masuk</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('client.register') }}">Daftar</a>
                </div>
            @endauth
        </div>

    </div>
</nav>

{{-- Di luar <nav>: backdrop-filter di navbar membuat elemen fixed di dalamnya ikut melebarkan halaman --}}
    <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header border-bottom">
            <h2 class="offcanvas-title h5" id="offcanvasNavbarLabel">Menu</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <ul class="navbar-nav">
                <li><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                <li><a class="nav-link" href="{{ $home }}#about">Tentang</a></li>
                <li><a class="nav-link" href="{{ $home }}#katalog">Katalog</a></li>
                <li><a class="nav-link" href="{{ route('client.planner') }}">Wedding Planner</a></li>
                @auth('client')
                    <li><a class="nav-link" href="{{ route('client.profile.index') }}">Profil Saya</a></li>
                    <li><a class="nav-link" href="{{ route('client.orders.history') }}">Riwayat Pesanan</a></li>
                @endauth
            </ul>

            <div class="mt-auto pt-4 d-grid gap-2">
                @auth('client')
                    <form method="POST" action="{{ route('client.logout') }}" class="d-grid">
                        @csrf
                        <button class="btn btn-outline-danger" type="submit">Keluar</button>
                    </form>
                @else
                    <a class="btn btn-primary" href="{{ route('client.register') }}">Daftar</a>
                    <a class="btn btn-outline-secondary" href="{{ route('client.login') }}">Masuk</a>
                @endauth
            </div>
        </div>
    </div>
