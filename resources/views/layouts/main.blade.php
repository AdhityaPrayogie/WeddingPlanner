<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#FAF8F5">
    <title>@yield('title', 'Takari Wedding Organizer')</title>
    <meta name="description" content="Takari Wedding Organizer — paket pernikahan elegan di Serang, Banten.">
    <link rel="icon" href="{{ asset('asset/img/Takari.png') }}" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php $fonts = 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@400;600&display=swap'; @endphp
    <link rel="preload" as="style" href="{{ $fonts }}">
    <link rel="stylesheet" href="{{ $fonts }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ $fonts }}"></noscript>

    <link rel="stylesheet" href="{{ asset('asset/css/style.css') }}?v={{ filemtime(public_path('asset/css/style.css')) }}">
    @if (Request::is('admin*'))
        <link rel="stylesheet" href="{{ asset('asset/css/dashboard.css') }}?v={{ filemtime(public_path('asset/css/dashboard.css')) }}">
    @endif

    @stack('styles')
</head>
<body>
    <a href="#main" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-primary">Lewati ke konten</a>

    @if (Request::is('admin*'))
        <nav class="navbar d-lg-none fixed-top admin-topbar">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary btn-sm btn-icon" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Buka menu admin">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <span class="navbar-brand mb-0">Takari Panel</span>
            </div>
        </nav>
        <div class="d-lg-none admin-topbar-spacer"></div>
    @else
        @include('components.navbar')
    @endif

    <main id="main">
        @yield('content')
    </main>

    @unless (Request::is('admin*'))
        @include('components.footer')
    @endunless

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        // Script di bawah jalan setelah Bootstrap (defer) siap
        // Reveal halus saat discroll (dimatikan otomatis oleh prefers-reduced-motion di CSS)
        document.addEventListener('DOMContentLoaded', () => {
            const els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) return els.forEach(el => el.classList.add('is-visible'));
            const io = new IntersectionObserver(entries => entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
            }), { threshold: .15 });
            els.forEach(el => io.observe(el));
        });

        // Tolak file kebesaran sebelum diunggah (server tetap memvalidasi). <input type="file" data-max-mb="5">
        document.addEventListener('change', e => {
            const input = e.target;
            if (!input.matches('input[type=file][data-max-mb]')) return;
            const max = Number(input.dataset.maxMb);
            const file = input.files[0];
            const tooBig = file && file.size > max * 1024 * 1024;
            input.setCustomValidity(tooBig ? `Ukuran file ${(file.size / 1048576).toFixed(1)} MB. Maksimal ${max} MB.` : '');
            input.classList.toggle('is-invalid', tooBig);
            if (tooBig) input.reportValidity();
        });
    </script>
    @stack('scripts')
</body>
</html>
