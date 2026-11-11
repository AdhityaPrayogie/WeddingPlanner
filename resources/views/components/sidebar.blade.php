@php
    $menu = [
        ['admin.dashboard', 'admin.dashboard', 'speedometer2', 'Dashboard'],
        ['admin.packages.index', 'admin.packages.*', 'box-seam', 'Paket'],
        ['admin.categories.index', 'admin.categories.*', 'tags', 'Kategori'],
        ['admin.planners.index', 'admin.planners.*', 'clipboard-heart', 'Wedding Planner'],
        ['admin.users.index', 'admin.users.*', 'people', 'Pengguna'],
        ['admin.profile.index', 'admin.profile.*', 'person-circle', 'Profil Saya'],
    ];
@endphp

@php ob_start(); @endphp
    <nav class="nav flex-column admin-nav" aria-label="Menu admin">
        @foreach ($menu as [$route, $pattern, $icon, $label])
            @if ($loop->last) <hr> @endif
            <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}"
                @if (request()->routeIs($pattern)) aria-current="page" @endif>
                <i class="bi bi-{{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <form action="{{ route('admin.logout') }}" method="POST" class="admin-logout">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm w-100">Keluar</button>
    </form>
@php $links = ob_get_clean(); @endphp

<aside class="d-none d-lg-block col-lg-3 col-xl-2 sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('asset/img/Takari.png') }}" alt="Takari" class="logo">
        <span class="eyebrow">Admin Panel</span>
    </div>
    {!! $links !!}
</aside>

<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title h5" id="mobileSidebarLabel">Menu Admin</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body px-0 pt-0">
        {!! $links !!}
    </div>
</div>
