<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Takari</title>
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
</head>
<body>
    <main class="auth-wrap">
        <div class="auth-card">
            <a href="{{ route('home') }}" class="d-block text-center">
                <img src="{{ asset('asset/img/Takari.png') }}" alt="Takari — kembali ke beranda" class="logo">
            </a>
            <span class="eyebrow text-center">@yield('eyebrow')</span>
            <h1 class="text-center">@yield('title')</h1>

            @if ($errors->any() || session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') ?? $errors->first() }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        // Tombol tampil/sembunyi password: <button data-toggle-password="id-input">
        document.querySelectorAll('[data-toggle-password]').forEach(btn => btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.togglePassword);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show);
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            btn.querySelector('i').className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
        }));
    </script>
</body>
</html>
