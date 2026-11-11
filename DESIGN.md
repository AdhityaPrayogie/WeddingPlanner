# Takari Design System — Modern Minimalist Luxury

Panduan desain **dan** development untuk semua tampilan Takari.
Implementasi: `public/asset/css/style.css` (global) dan `public/asset/css/dashboard.css` (khusus admin).
Kalau dokumen ini dan CSS berbeda, **CSS yang benar**. Perbarui dokumen ini di commit yang sama.

---

## 0. Prinsip

1. **Ruang kosong adalah fitur.** Kalau ragu, tambah jarak, bukan elemen.
2. **Satu aksen.** Rose gold hanya untuk eyebrow, harga, link, garis tipis, dan hover tombol.
3. **Tipografi yang memimpin.** Hierarki dibangun dari ukuran dan jenis huruf, bukan warna atau bayangan.
4. **Halus, bukan ramai.** Garis 1px, bayangan nyaris tak terlihat, sudut 2–4px, gerak pelan.
5. **Foto adalah bintangnya.** UI mundur, foto paket maju.

---

## 1. Aturan development (wajib)

| Boleh | Jangan |
|---|---|
| Pakai token `var(--…)` di CSS | Tulis hex atau px langsung di aturan komponen |
| Pakai class komponen di bagian 8 | `style="…"` di Blade |
| Utilitas Bootstrap spasi `0–5` (lihat 4.2) | `fs-*`, `fw-bold`, `display-*`, `text-white`, `bg-primary`, `shadow-lg` sebagai styling |
| `<h1>` sekali per halaman, urutan heading tidak loncat | Memilih tag heading karena ukurannya. Pakai class `.h2`/`.h3` untuk ukuran |
| Ikon dekoratif `aria-hidden="true"`, tombol ikon pakai `aria-label` | Tombol yang isinya hanya ikon tanpa label |
| `<label for>` di setiap input | Placeholder sebagai pengganti label |
| `loading="lazy"` + `width`/`height` di gambar | Gambar tanpa `alt` |

Butuh nilai baru? Tambah token di `:root` dulu, lalu dokumentasikan di sini.

---

## 2. Warna

### 2.1 Palet

| Token | Hex | Pakai untuk |
|---|---|---|
| `--ivory` | `#FAF8F5` | Latar halaman |
| `--surface` | `#FFFFFF` | Kartu, panel, modal, tabel, input |
| `--sand` | `#F1ECE6` | Section selang-seling, catatan (`.note`), latar input-group |
| `--line` | `#E6DFD7` | Semua border dan divider (1px) |
| `--line-strong` | `#CFC6BC` | Border tombol outline, chip filter |
| `--ink` | `#1F1C1A` | Judul, teks utama, tombol primary, footer |
| `--ink-soft` | `#5C5550` | Teks pendukung, label form, header tabel |
| `--ink-mute` | `#8F8781` | Placeholder saja |
| `--rose` | `#B76E79` | Dekorasi: garis judul, ikon statistik, indikator aktif |
| `--rose-deep` | `#8F4F5A` | Eyebrow, harga, link, hover tombol primary |
| `--blush` | `#F7E4E2` | Focus ring, seleksi teks |
| `--champagne` | `#C9A96E` | Cadangan. Maks. 1 kali per halaman |

### 2.2 Status

| Status | Teks | Latar | Kontras |
|---|---|---|---|
| Sukses / Dikonfirmasi | `--success` `#3F6649` | `--success-bg` `#E8F0EA` | 5.6:1 |
| Menunggu | `--warning` `#7A5A1A` | `--warning-bg` `#F6EEDC` | 5.5:1 |
| Gagal / Dibatalkan | `--danger` `#A14545` | `--danger-bg` `#F6E3E3` | 4.9:1 |

### 2.3 Kontras (sudah dihitung)

| Kombinasi | Rasio | Boleh untuk |
|---|---|---|
| `--ink` di `--ivory` | 16.0:1 | Semua teks |
| `--ink-soft` di `--ivory` | 6.9:1 | Semua teks |
| `--rose-deep` di putih | 6.1:1 | Semua teks |
| `--rose` di putih | 3.8:1 | Teks ≥ 24px, ikon, dekorasi |
| `--ink-mute` di `--ivory` | 3.3:1 | Placeholder saja, **bukan** teks informasi |

Komposisi: ±90% ivory/putih/ink, ±10% aksen.

---

## 3. Tipografi

### 3.1 Font

| Peran | Token | Font | Bobot yang dipakai |
|---|---|---|---|
| Display (h1–h4, judul modal, angka statistik) | `--font-display` | Playfair Display | 500 (`--fw-medium`) |
| Body / UI (paragraf, label, tombol, tabel, h5–h6) | `--font-body` | Inter | 400, 600 |

Dimuat dari Google Fonts di `layouts/main.blade.php` dan `layouts/auth.blade.php` (`wght@500;700` dan `400;600`). Jangan tambah bobot lain tanpa memperbarui URL font.

### 3.2 Skala ukuran (rasio 1.25, basis 16px)

| Token | Ukuran | Dipakai oleh |
|---|---|---|
| `--fs-xs` | 12px | Eyebrow, tombol, badge, header tabel, tab, menu navbar |
| `--fs-sm` | 14px | Label form, isi tabel, alert, teks footer, sidebar |
| `--fs-base` | 16px | Body, input, harga di kartu |
| `--fs-md` | 18px | `.lead`, teks hero |
| `--fs-lg` | 20px | `h4`, `.price-lg`, judul toolbar admin |
| `--fs-xl` | 22–25px | `h3`, judul kartu paket, judul modal |
| `--fs-2xl` | 28–36px | `h2`, `.page-title`, `.section-title`, angka statistik |
| `--fs-3xl` | 32–48px | `h1` |
| `--fs-display` | 40–72px | Judul hero saja |

Ukuran ≥ `--fs-xl` memakai `clamp()`, jadi otomatis mengecil di HP. Tidak perlu media query untuk ukuran huruf.

### 3.3 Line-height & tracking

| Token | Nilai | Untuk |
|---|---|---|
| `--lh-tight` | 1.15 | h1, h2 |
| `--lh-snug` | 1.3 | h3–h6, label, input |
| `--lh-base` | 1.6 | Body default, tabel |
| `--lh-relaxed` | 1.75 | Paragraf panjang, `.lead`, footer |
| `--tracking-tight` | -0.015em | Semua judul Playfair |
| `--tracking-wide` | 0.12em | Tombol, chip, tab (huruf kapital) |
| `--tracking-wider` | 0.2em | Eyebrow (huruf kapital) |

### 3.4 Aturan teks

- Paragraf maks. `65ch`, rata kiri. Jangan `text-justify`.
- Huruf kapital **hanya** untuk teks kecil (≤ 12px) dengan tracking lebar: eyebrow, tombol, badge, header tabel.
- Judul Playfair tidak pernah kapital semua.
- Angka (harga, tanggal, jumlah) pakai `.tabular` atau `.price`. Format harga: `Rp 20.000.000` (ada spasi).

---

## 4. Spasi & padding

### 4.1 Skala (kelipatan 4px)

| Token | px | Contoh pemakaian |
|---|---|---|
| `--space-1` | 4 | Jarak label statistik ke angka |
| `--space-2` | 8 | Gap antar tombol, ikon ke teks, label ke input |
| `--space-3` | 12 | Padding vertikal tombol & input, jarak eyebrow ke judul |
| `--space-4` | 16 | Gutter HP, gap antar field kecil, padding sel tabel |
| `--space-5` | 24 | Padding kartu, gap grid kartu, jarak antar field |
| `--space-6` | 32 | Padding panel (HP), padding modal, gutter desktop |
| `--space-7` | 48 | Padding panel (desktop), jarak header section ke isi |
| `--space-8` | 64 | Padding section (HP), padding atas footer |
| `--space-9` | 96 | Padding section (desktop), jarak konten ke footer |

### 4.2 Pemetaan utilitas Bootstrap

Utilitas Bootstrap tetap boleh, tapi **hanya** angka ini, yang kebetulan cocok dengan skala:

| Bootstrap | px | = token |
|---|---|---|
| `*-1` | 4 | `--space-1` |
| `*-2` | 8 | `--space-2` |
| `*-3` | 16 | `--space-4` |
| `*-4` | 24 | `--space-5` |
| `*-5` | 48 | `--space-7` |

Ritme vertikal standar:
- Antar field form: `mb-4` (24px). Field terakhir sebelum tombol: `mb-5` (48px), atau beri `mt-5` pada baris tombol.
- Antar tombol: `gap-2` (8px).
- Grid: pakai class grid komponen (`.package-grid`, `.stat-grid`, `.gallery`), atau `gy-5 gx-lg-5` untuk layout dua kolom.

### 4.3 Padding komponen

| Token | Nilai | Komponen |
|---|---|---|
| `--pad-btn-x` | 24px | Tombol (16px untuk `.btn-sm`) |
| `--pad-control-x` | 16px | Input, select, textarea |
| `--pad-card` | 24px | `.card`, isi `.package-card` |
| `--pad-panel` | 32px → 48px (≥ 992px) | `.panel`. 24px di bawah 576px |
| `--pad-cell-y` / `-x` | 16px / 16px | Sel tabel (header 12px vertikal) |
| `--gutter` | 16px → 32px (≥ 992px) | Padding samping `.container` |

### 4.4 Ukuran kontrol

| Token | Tinggi | Untuk |
|---|---|---|
| `--control-h` | 48px | Tombol, input, select |
| `--control-h-sm` | 36px | `.btn-sm`, `.btn-icon` |

Area sentuh min. 44×44px. Tombol ikon di tabel memakai 36px karena berada di dalam baris tabel yang tingginya ≥ 56px.

### 4.5 Layout

- Lebar konten maks. `--container-max` 1200px.
- Section halaman publik: `.section` (64px di HP, 96px di desktop).
- Grid kartu paket: 1 kolom (< 576px), 2 kolom (≥ 576px), 3 kolom (≥ 992px), 4 kolom (≥ 1200px). Gap 16px, 24px di ≥ 992px.
- Form/akun: `.page-narrow` (480px). Konten sedang: `.page-medium` (720px).

---

## 5. Bentuk, bayangan, gerak

| Token | Nilai | Untuk |
|---|---|---|
| `--radius-sm` | 2px | Input, badge, alert, thumbnail, catatan |
| `--radius` | 4px | Kartu, panel, modal, tabel, gambar |
| `--radius-btn` | 999px | Semua tombol (pill), chip filter. `.btn-icon` jadi lingkaran. Tombol di dalam `.input-group` tetap `--radius-sm` |
| `--shadow-sm` | `0 1px 2px rgba(31,28,26,.04)` | Hampir tidak dipakai |
| `--shadow-md` | `0 12px 32px -12px rgba(31,28,26,.14)` | Hover kartu, modal, dropdown, tombol WhatsApp |
| `--dur-fast` | 200ms | Warna tombol, link, chip |
| `--dur` | 400ms | Bayangan kartu, garis navbar |
| `--dur-slow` | 600ms | Zoom foto, reveal section |
| `--ease` | `cubic-bezier(.2,.6,.2,1)` | Semua transisi |

- Tombol selalu pill (`--radius-btn`) sebagai kontras lembut terhadap kartu dan panel yang bersudut tegas (2–4px). Jangan pakai sudut di antaranya (8–16px).
- Pemisah = border 1px, bukan bayangan.
- `prefers-reduced-motion` mematikan semua animasi (sudah di CSS).

---

## 6. Hierarki heading per halaman

| Halaman | h1 | h2 | h3 |
|---|---|---|---|
| Home | Judul hero | Judul section (`.section-title`) | Nama paket di kartu |
| Auth (masuk/daftar) | Judul kartu | — | — |
| Pembayaran | Nama paket | "Lengkapi detail acara" (`.page-title`) | — |
| Profil / Riwayat | `.page-title` | — | — |
| Admin | `.page-title` di `.page-header` | Judul `.toolbar` | — |
| Modal | — | `.modal-title` | — |

---

## 7. Pola halaman (copy-paste)

### Halaman client

```blade
@extends('layouts.main')
@section('title', 'Judul | Takari')

@section('content')
<div class="container section">
    <header class="page-header">
        <div>
            <span class="eyebrow">Akun</span>
            <h1 class="page-title">Judul Halaman</h1>
        </div>
        <div class="page-actions">{{-- tombol opsional --}}</div>
    </header>

    <div class="panel">…</div>
</div>
@endsection
```

### Halaman admin

```blade
@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main">
            <header class="page-header">
                <div>
                    <span class="eyebrow">Katalog</span>
                    <h1 class="page-title">Judul</h1>
                </div>
            </header>

            <div class="toolbar">
                <h2>Daftar Data</h2>
                <button class="btn btn-primary btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">…</table>
            </div>
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection
```

### Halaman auth

```blade
@extends('layouts.auth')
@section('title', 'Masuk')
@section('eyebrow', 'Selamat Datang Kembali')

@section('content')
<form method="POST" action="…">
    @csrf
    <div class="mb-4">…field…</div>
    <div class="mb-5">…field terakhir…</div>
    <button class="btn btn-primary w-100">Masuk</button>
    <p class="auth-foot">Belum punya akun? <a href="…">Daftar</a></p>
</form>
@endsection
```

### Field form

```blade
<div class="mb-4">
    <label for="email" class="form-label">Email <span class="optional">(opsional)</span></label>
    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email"
        class="form-control @error('email') is-invalid @enderror"
        @error('email') aria-describedby="email-error" @enderror>
    @error('email') <div class="invalid-feedback" id="email-error">{{ $message }}</div> @enderror
</div>
```

### Aksi di baris tabel

```blade
<td class="col-actions">
    <div class="table-actions">
        <button class="btn btn-outline-secondary btn-sm btn-icon" aria-label="Edit {{ $row->nama }}">
            <i class="bi bi-pencil" aria-hidden="true"></i>
        </button>
        <form method="POST" action="…">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm btn-icon" aria-label="Hapus {{ $row->nama }}"
                onclick="return confirm('Yakin?')"><i class="bi bi-trash" aria-hidden="true"></i></button>
        </form>
    </div>
</td>
```

---

## 8. Referensi class

### Tipografi

| Class | Hasil |
|---|---|
| `.eyebrow` | Label kecil kapital rose-deep di atas judul (12px, tracking 0.2em) |
| `.page-title` | Judul halaman (`--fs-2xl`, Playfair) |
| `.section-title` | Judul section + garis rose 48px di bawahnya |
| `.lead` | Paragraf pembuka 18px, `--ink-soft` |
| `.price` / `.price-lg` | Harga 16px / 20px, tabular, rose-deep |
| `.tabular` | Angka sejajar |
| `.text-xs` | 12px |
| `.optional` | Teks "(opsional)" di label |

### Layout

| Class | Hasil |
|---|---|
| `.section` | Padding vertikal 64px / 96px |
| `.section-header` | Blok judul section, jarak bawah 48px |
| `.page-header` | Judul halaman + aksi, garis bawah, jarak bawah 32/48px |
| `.page-actions` | Grup tombol di `.page-header` |
| `.page-narrow` / `.page-medium` | Lebar maks. 480 / 720px, tengah |
| `.toolbar` | Baris judul tabel + tombol (admin) |
| `.admin-main` | Area konten admin (padding 48px / 32px HP) |
| `.bg-sand` | Latar section selang-seling |

### Komponen

| Class | Hasil |
|---|---|
| `.panel` | Kotak putih ber-border untuk form & konten |
| `.note` | Catatan latar sand (mis. info rekening) |
| `.package-card` (+ `.media`, `.body`, `.summary`, `.actions`) | Kartu paket (foto 4:3, ringkasan 2 baris, 3 fitur) |
| `.media-tag` / `.media-flag` | Label kategori (kiri atas) / penanda "Terlengkap" (kanan atas) di foto |
| `.features` / `.features-full` | Daftar fasilitas bergaris rose. Di kartu 1 baris per item, di modal penuh |
| `.price-label` | Label kecil "Harga" di atas `.price` |
| `.package-detail`, `.detail-media`, `.detail-body` | Modal detail paket: foto kiri, info kanan |
| `.package-grid` | Grid kartu paket (gap 24px) |
| `.category-filter` | Chip filter kategori (`aria-pressed`) |
| `.stat-grid`, `.stat-card`, `.stat-label`, `.stat-value` | Kartu statistik (admin & planner) |
| `.progress-line` | Garis progres tipis; isi lewat `style="--v: 40%"` |
| `.countdown` (+ `-num`, `-label`) | Hitung mundur hari H di header planner |
| `.task-row`, `.task-check`, `.task-note`, `.task-actions` | Baris checklist: centang bulat, catatan, tombol edit/hapus |
| `.rsvp-select` (+ `.rsvp-hadir/-tidak/-pending`) | Pilihan kehadiran berwarna status |
| `.thumb` / `.thumb-lg` | Thumbnail 56×56 / 80×100 |
| `.cell-clamp` | Potong teks tabel jadi 2 baris |
| `.col-actions`, `.table-actions` | Kolom aksi tabel rata kanan |
| `.icon-xl` | Ikon besar 48px (halaman sukses) |
| `.reveal` | Fade-in saat discroll (otomatis via JS di layout) |

### Tombol

| Class | Pakai untuk |
|---|---|
| `.btn-primary` | Satu aksi utama per layar/kartu. Hitam, hover rose-deep |
| `.btn-outline-secondary` | Aksi kedua: Kembali, Batal, Edit, filter |
| `.btn-danger` | Hapus (outline merah) |
| `.btn-success` / `.btn-warning` | **Hanya** tombol ubah status order |
| `.btn-link` | Aksi tersier (mis. "Detail") |
| `.btn-light` | Tombol di atas foto hero |
| `.btn-sm` | Tombol di toolbar, navbar, dan tabel |
| `.btn-icon` | Tombol persegi berisi ikon (wajib `aria-label`) |

Teks tombol: kata kerja singkat ("Simpan", "Pesan", "Masuk"), bukan "Klik di sini".

### Badge status

```blade
<span class="badge bg-success">Dikonfirmasi</span>
<span class="badge bg-warning">Menunggu</span>
<span class="badge bg-danger">Dibatalkan</span>
```

---

## 9. Katalog paket

- **Deskripsi paket** ditulis admin dengan format: satu atau dua kalimat ringkasan, lalu daftar bernomor (`1.`, `2.` atau `1)`). `Package::rincian` memecahnya jadi `ringkasan` + `fitur[]`. Tanpa daftar bernomor, semua teks jadi ringkasan.
- **Kartu** menampilkan: label kategori, nama, ringkasan (2 baris), 3 fitur pertama, harga, tombol Detail. Tombol Pesan hanya ada di modal, supaya kartu tetap tenang.
- **"Terlengkap"** otomatis di paket termahal per kategori, hanya bila kategori punya ≥ 2 paket.
- **Filter** menampilkan jumlah paket per kategori. Hasil filter diumumkan ke pembaca layar lewat `#katalog-count` (`aria-live`).

## 10. Foto

- Rasio tetap: kartu paket 4:3, galeri 4:5, hero memenuhi layar dengan `object-fit: cover`.
- Semua upload lewat `App\Support\Media` (lihat bagian 11).
- Teks di atas foto wajib pakai overlay gradasi (sudah ada di `.hero`).
- `alt` deskriptif: "Paket Silver — Decoration", bukan "Gambar".

---

## 11. Upload media & performa

| Jenis | Batas | Format | Proses otomatis |
|---|---|---|---|
| Gambar (paket, bukti bayar) | **5 MB** | JPG, PNG, WebP | Diubah ke WebP kualitas 78, lebar maks. 1600px, rotasi foto HP diperbaiki. Paket juga dapat versi kecil `-sm.webp` 640px |
| Video paket (opsional) | **10 MB** | MP4, WebM, MOV | ffmpeg ke MP4 H.264 720p, CRF 28, `faststart`. Bila ffmpeg tidak ada / hasil lebih besar, file asli disimpan |

- Aturan validasi & pesan error: `Media::imageRule()`, `Media::videoRule()`, `Media::messages()`. Jangan tulis `max:` sendiri.
- Di browser, `<input type="file" data-max-mb="5">` menolak file kebesaran sebelum diunggah (script di `layouts/main`). Server tetap memvalidasi.
- Path ffmpeg: `FFMPEG_BINARY` di `.env`.
- Gambar lama: `php artisan media:optimize` (aman dijalankan ulang).
- Tampilkan gambar paket dengan `srcset` (`gambar_kecil_url` 640w + `gambar_url` 1200w) dan `sizes`. Gambar pertama di layar: `fetchpriority="high"`, sisanya `loading="lazy" decoding="async"`, selalu dengan `width`/`height`.
- Gambar statis landing: `<picture>` dengan `.webp` + `-sm.webp`, fallback `.jpg`.
- Video: `preload="none"`, `playsinline`, `poster` = gambar paket.
- Cache: `public/.htaccess` meng-cache gambar/video 1 tahun, CSS/JS 7 hari. CSS di-versi lewat `?v=filemtime`, jadi perubahan langsung terlihat.
- Font Google dimuat async (tidak menahan render), Bootstrap JS `defer`.

## 12. Responsive

Diuji di 320, 390, 768, 1024, 1440, 1920px: tidak boleh ada scroll horizontal.

| Breakpoint | Perubahan |
|---|---|
| ≤ 375px | Padding tombol & kartu dikecilkan, header halaman bertumpuk, tombol modal melebar |
| < 576px | Modal form jadi layar penuh (`modal-fullscreen-sm-down`) |
| < 768px | Tabel `.table-stack-sm` berubah jadi kartu bertumpuk |
| 768–1199px | Tabel lebar `.table-stack-lg` jadi grid kartu 2 kolom |
| < 1600px | Kolom `.col-optional` (di `.table-stack-lg`) disembunyikan di mode tabel |
| < 992px | Sidebar admin jadi menu offcanvas, gutter 16px |
| ≥ 1200px | Katalog 4 kolom |
| ≥ 1600px | Lebar konten 1320px |

- Tabel baru: bungkus `<div class="table-responsive table-stack-sm">`, beri `data-label="Judul Kolom"` di tiap `<td>` data, dan `class="col-actions"` di sel aksi.
- Jangan taruh elemen `position: fixed` (offcanvas, modal) di dalam elemen ber-`backdrop-filter` seperti `.site-nav`: browser memperlakukannya sebagai wadah dan halaman jadi melebar.
- Area notch iPhone ditangani lewat `env(safe-area-inset-*)` (`viewport-fit=cover`).

## 13. Wedding Planner (`/client/planner`)

Fitur client yang sudah login, menu "Wedding Planner" di navbar & footer. Satu planner per client (dibuat otomatis saat pertama dibuka).

| Tab | Isi |
|---|---|
| Ringkasan | Nama pasangan, tanggal, lokasi, total anggaran, target tamu · 4 kartu progres · 5 tugas berikutnya |
| Checklist | 25 tugas template dibagi 7 tahap (12 bulan sebelum s.d. Hari H). Target tanggal dihitung dari tanggal pernikahan dan dijadwal ulang saat tanggal diubah. Tugas terlambat ditandai merah + angka di tab. Tiap tugas bisa diberi **catatan** (maks. 2000 karakter, baris baru dipertahankan) lewat tombol edit; catatan tampil di bawah judul dengan garis blush (`.task-note`) |
| Anggaran | Pos per kategori, estimasi vs dibayar, status Belum/DP/Lunas, sisa/lebih dari total. Nama item bisa diisi dari saran paket katalog Takari |
| Tamu | Nama, pihak, jumlah orang, No. HP (tautan WhatsApp), RSVP langsung di tabel, pencarian nama |

- **Reset Planner** (bagian bawah tab Ringkasan, `DELETE /client/planner`): menghapus planner + semua tugas, catatan, anggaran, dan tamu lewat FK cascade. Wajib ketik `RESET`. Tombol hapus baru aktif setelah teksnya tepat, dan server memvalidasi ulang. Saat halaman dibuka lagi, planner baru dibuat dengan checklist template.
- **Admin → menu Wedding Planner** (`/admin/planners`): daftar semua planner client (pernikahan terdekat di atas), cari nama pasangan/client, ringkasan: total, menikah ≤ 3 bulan, belum isi tanggal. Detail `/admin/planners/{id}`: kontak client + tautan WhatsApp, progres, pos anggaran, checklist & catatan, tamu. **Hanya-lihat**, admin tidak bisa mengubah planner client.
- Nomor HP → tautan WhatsApp lewat `App\Support\Phone::waLink()`. Nomor tidak valid ("-", terlalu pendek) tampil sebagai teks biasa.
- Data: tabel `planners`, `planner_tasks`, `planner_budget_items`, `planner_guests`. Konstanta tahap/kategori/RSVP di `App\Models\Planner`.
- Keamanan: semua query lewat `PlannerController::planner()` milik client yang login; `client_id` tidak fillable. Diuji di `tests/Feature/PlannerTest.php`.
- Tombol WhatsApp melayang disembunyikan di halaman planner (menutupi tombol aksi di HP); kontak tetap di footer.
- Uang & jumlah: input `type="number"` pakai `step="1"`. Jangan pakai step lain: angka yang tidak kelipatan step ditolak browser tanpa pesan yang jelas.

## 14. Ikon

`bootstrap-icons` 1.11. Ukuran mengikuti teks (`1.1em` di tombol). Pakai versi outline (`bi-pencil`, `bi-trash`), bukan `-fill`, kecuali untuk status (`bi-check2-circle`).

---

## 15. File

| File | Isi |
|---|---|
| `public/asset/css/style.css` | Token, dasar, tombol, form, tabel, navbar, home, auth, footer |
| `public/asset/css/dashboard.css` | Sidebar, topbar, toolbar, statistik admin (dimuat hanya di `/admin*`) |
| `resources/views/layouts/main.blade.php` | Layout publik + admin, script reveal |
| `resources/views/layouts/auth.blade.php` | Layout masuk/daftar, toggle password |
| `resources/views/components/*` | Navbar, footer, sidebar |
| `resources/views/admin/_paket-form.blade.php` | Form paket (tambah & edit) |
| `app/Support/Media.php` | Validasi, kompres, simpan gambar & video |
| `tests/Unit/MediaTest.php` | Uji batas ukuran & kompresi |
| `resources/views/client/planner.blade.php` | Halaman Wedding Planner (4 tab) |
| `tests/Feature/PlannerTest.php` | Uji planner, reset, halaman admin, isolasi data antar client |
| `resources/views/admin/planner/*` | Daftar & detail planner untuk admin |

---

## 16. Checklist sebelum merge

- [ ] Tidak ada `style="…"` baru di Blade (`grep -rn 'style="' resources/views`)
- [ ] Tidak ada hex baru di luar `:root`
- [ ] Satu `<h1>` per halaman, heading tidak loncat level
- [ ] Spasi hanya dari skala (utilitas `0–5` atau token)
- [ ] Satu tombol primary per layar/kartu
- [ ] Semua input punya `<label for>`, tombol ikon punya `aria-label`
- [ ] Gambar punya `alt`, `loading="lazy"`, `width`/`height`
- [ ] Rapi di 320px–1920px tanpa scroll horizontal
- [ ] Upload baru memakai `App\Support\Media`
- [ ] Fokus keyboard terlihat di semua elemen interaktif
