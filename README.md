# Takari Wedding Organizer

Aplikasi web untuk **Takari Wedding Organizer** (Serang, Banten): katalog paket pernikahan, pemesanan dengan upload bukti bayar, dan **Wedding Planner** pribadi untuk calon pengantin. Admin mengelola paket, pesanan, pengguna, dan memantau planner client.

Dibangun dengan Laravel 9 dan Bootstrap 5, dengan tema **Modern Minimalist Luxury** (lihat [`DESIGN.md`](DESIGN.md)).

---

## Daftar Isi

1. [Fitur](#fitur)
2. [Teknologi](#teknologi)
3. [Kebutuhan Sistem](#kebutuhan-sistem)
4. [Instalasi Lokal](#instalasi-lokal)
5. [Konfigurasi `.env`](#konfigurasi-env)
6. [Akun & Akses](#akun--akses)
7. [Upload Media](#upload-media)
8. [Perintah Artisan](#perintah-artisan)
9. [Testing](#testing)
10. [Struktur Project](#struktur-project)
11. [Database](#database)
12. [Deploy ke Production](#deploy-ke-production)
13. [Keamanan](#keamanan)
14. [Troubleshooting](#troubleshooting)

---

## Fitur

### Pengunjung

- Landing page: hero, tentang kami, galeri.
- **Katalog paket** dengan filter kategori, label "Terlengkap", ringkasan fasilitas, dan modal detail (foto/video).
- Daftar & masuk akun client.

### Client (setelah login)

- **Pemesanan paket**: pilih tanggal acara, catatan, upload bukti transfer.
- **Riwayat pesanan** beserta status (Menunggu / Dikonfirmasi / Dibatalkan) dan bukti bayar.
- **Wedding Planner** (`/client/planner`), terdiri dari 4 tab:

  | Tab | Isi |
  |---|---|
  | Ringkasan | Nama pasangan, tanggal, lokasi, total anggaran, target tamu, hitung mundur hari H, kartu progres, 5 tugas berikutnya |
  | Checklist | 25 tugas bawaan dalam 7 tahap (12 bulan sebelum s.d. Hari H). Target tanggal dihitung otomatis. Tugas terlambat ditandai merah. Tiap tugas bisa diberi **catatan** |
  | Anggaran | Pos per kategori, estimasi vs dibayar, status Belum/DP/Lunas, peringatan bila melebihi anggaran |
  | Tamu | Daftar tamu per pihak, jumlah orang, tautan WhatsApp, RSVP langsung di tabel, pencarian |

  Tersedia juga tombol **Reset Planner** untuk menghapus semua data planner dan mulai dari awal (wajib mengetik `RESET`).
- Profil akun.

### Admin

- **Dashboard**: statistik pesanan, ubah status, hapus, lihat bukti bayar.
- **Paket**: tambah/edit/hapus, upload gambar & video (dikompres otomatis).
- **Kategori**: kelola kategori paket.
- **Wedding Planner**: daftar planner semua client (pernikahan terdekat di atas), pencarian, dan detail lengkap. Halaman ini **hanya untuk dilihat**, admin tidak bisa mengubah planner client.
- **Pengguna**: kelola akun admin & client.
- Profil & ganti password.

### Teknis

- Responsif dari 320px sampai 1920px. Tabel otomatis berubah jadi kartu di layar kecil.
- Gambar dikonversi ke WebP + versi kecil untuk HP (`srcset`), lazy loading, dan cache browser.
- Bukti pembayaran disimpan **privat** dan hanya bisa dibuka pemiliknya atau admin.
- Login dibatasi 5 percobaan/menit.

---

## Teknologi

| Bagian | Dipakai |
|---|---|
| Backend | PHP 8.1+, Laravel 9 |
| Database | MySQL 5.7+ / MariaDB 10.3+ |
| Frontend | Blade, Bootstrap 5.3 & Bootstrap Icons (CDN), CSS kustom di `public/asset/css/` |
| Font | Playfair Display & Inter (Google Fonts) |
| Media | Ekstensi PHP GD (gambar), ffmpeg (video, opsional) |

> Tidak ada proses build frontend. CSS/JS dimuat langsung dari `public/` dan CDN, jadi `npm install` **tidak diperlukan**.

---

## Kebutuhan Sistem

- **PHP 8.1 atau lebih baru** dengan ekstensi: `gd` (dukungan WebP), `exif`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- **Composer 2**
- **MySQL / MariaDB**
- **ffmpeg** (opsional, untuk kompres video. Tanpa ffmpeg, video tetap tersimpan tanpa dikompres)
- Laragon atau XAMPP untuk lokal, atau server Apache/Nginx

Cek ekstensi PHP:

```bash
php -m | grep -iE "gd|exif|pdo_mysql|mbstring|fileinfo"
php -r "var_dump(gd_info()['WebP Support']);"
```

---

## Instalasi Lokal

```bash
# 1. Ambil kode
git clone https://github.com/USERNAME/NAMA-REPO.git takari-wo
cd takari-wo

# 2. Pasang dependensi PHP
composer install

# 3. Siapkan konfigurasi
cp .env.example .env
php artisan key:generate
```

4. **Buat database** kosong (misalnya `takariwo`) lewat phpMyAdmin / HeidiSQL, lalu isi bagian database di `.env` (lihat [Konfigurasi](#konfigurasi-env)).

```bash
# 5. Buat tabel + akun admin pertama
php artisan migrate --seed

# 6. Hubungkan folder upload ke public/
php artisan storage:link

# 7. Jalankan
php artisan serve
```

Buka **http://127.0.0.1:8000**.

> **Akun admin:** isi `ADMIN_PASSWORD` di `.env` sebelum langkah 5. Kalau dikosongkan, password acak dibuat dan **ditampilkan sekali** di terminal. Catat saat itu juga.

### Memakai Laragon

Laragon membuat domain `http://takari-wo.test` otomatis. Pastikan **document root mengarah ke folder `public/`**:
Menu → Apache → `sites-enabled` → ubah `DocumentRoot` menjadi `.../takari-wo/public`. Lihat [Keamanan](#keamanan) untuk alasannya.

---

## Konfigurasi `.env`

| Kunci | Contoh | Keterangan |
|---|---|---|
| `APP_NAME` | `"Takari Wedding Organizer"` | Nama aplikasi |
| `APP_ENV` | `local` / `production` | Wajib `production` di server |
| `APP_DEBUG` | `true` / `false` | **Wajib `false` di server** (kalau `true`, detail error dan isi env bisa bocor) |
| `APP_URL` | `http://127.0.0.1:8000` | URL dasar aplikasi |
| `DB_DATABASE` | `takariwo` | Nama database |
| `DB_USERNAME` / `DB_PASSWORD` | `root` / | Kredensial database |
| `ADMIN_USERNAME` | `admin` | Username admin pertama (seeder) |
| `ADMIN_EMAIL` | `admin@example.com` | Email admin pertama |
| `ADMIN_PASSWORD` | *(kosong)* | Kosong = dibuat acak saat seed |
| `FFMPEG_BINARY` | `ffmpeg` atau path lengkap | Lokasi ffmpeg untuk kompres video |

Contoh path ffmpeg di Windows (pakai garis miring biasa):

```
FFMPEG_BINARY="C:/ffmpeg/bin/ffmpeg.exe"
```

---

## Akun & Akses

| Peran | URL | Guard | Tabel |
|---|---|---|---|
| Admin | `/admin/login` | `admin` | `users` |
| Client | `/client/login` (daftar: `/client/register`) | `client` | `clients` |

- Admin dan client punya sesi login terpisah (lihat `config/auth.php`).
- Admin tambahan dibuat lewat menu **Pengguna**. Admin tidak bisa menghapus akunnya sendiri.
- Lupa password admin? Jalankan:

  ```bash
  php artisan tinker
  >>> App\Models\User::where('username','admin')->first()->update(['password' => bcrypt('PasswordBaru123')]);
  ```

---

## Upload Media

Semua upload diproses `app/Support/Media.php`.

| Jenis | Batas | Format | Diproses menjadi | Disimpan di |
|---|---|---|---|---|
| Gambar paket | **5 MB** | JPG, PNG, WebP | WebP, lebar maks. 1600px + versi kecil 640px (`-sm.webp`) | `storage/app/public/uploads` (publik) |
| Video paket | **10 MB** | MP4, WebM, MOV | MP4 H.264 720p (bila ada ffmpeg) | `storage/app/public/videos` (publik) |
| Bukti bayar | **5 MB** | JPG, PNG, WebP | WebP, lebar maks. 1600px | `storage/app/bukti` (**privat**) |

- Foto dari HP yang miring otomatis diputar dengan benar.
- File yang kebesaran langsung ditolak di browser sebelum diunggah, dan server tetap memvalidasi ulang.
- Pastikan batas PHP cukup (`php.ini`):

  ```ini
  upload_max_filesize = 12M
  post_max_size = 12M
  memory_limit = 512M
  ```

---

## Perintah Artisan

| Perintah | Fungsi |
|---|---|
| `php artisan migrate --seed` | Buat tabel + admin pertama |
| `php artisan storage:link` | Hubungkan `public/storage` ke `storage/app/public` (wajib agar gambar tampil) |
| `php artisan media:optimize` | Ubah gambar lama (JPG/PNG) jadi WebP terkompres + thumbnail. Aman dijalankan ulang |
| `php artisan test` | Jalankan semua tes |
| `php artisan optimize` | Cache config & route (production) |
| `php artisan optimize:clear` | Hapus semua cache (bila perubahan tidak muncul) |

---

## Testing

Tes memakai database **terpisah** `takariwo_test`, yang dikosongkan setiap kali tes jalan. Jangan arahkan ke database utama.

```bash
# Sekali saja: buat database tes
mysql -u root -e "CREATE DATABASE IF NOT EXISTS takariwo_test"

# Jalankan
php artisan test
```

| File | Menguji |
|---|---|
| `tests/Feature/PlannerTest.php` | Planner, checklist & catatan, anggaran, RSVP, reset, halaman admin, **isolasi data antar client** |
| `tests/Unit/MediaTest.php` | Batas ukuran 5/10 MB, kompresi gambar & video |
| `tests/Unit/PhoneTest.php` | Pembuatan tautan WhatsApp dari nomor HP |

---

## Struktur Project

```
app/
├── Console/Commands/OptimizeMedia.php   # php artisan media:optimize
├── Http/Controllers/
│   ├── Admin/      # Dashboard, Paket, Kategori, Planner (lihat), Pengguna, Profil
│   ├── Auth/       # Login admin, login & register client
│   ├── Client/     # Pembayaran, Riwayat, Planner, Profil
│   └── HomeController.php
├── Models/         # User, Client, Category, Package, Order, Planner(+Task, BudgetItem, Guest)
└── Support/
    ├── Media.php   # Validasi & kompres upload
    └── Phone.php   # Nomor HP → tautan WhatsApp
public/
├── asset/css/style.css       # Tema global (token warna, tipografi, komponen)
├── asset/css/dashboard.css   # Khusus panel admin
└── .htaccess                 # Rewrite, cache, kompresi
resources/views/
├── layouts/        # main (umum & admin), auth (masuk/daftar)
├── components/     # navbar, footer, sidebar admin
├── admin/          # halaman admin (+ planner/)
├── client/         # halaman client (+ planner.blade.php)
└── home.blade.php  # landing & katalog
DESIGN.md           # Panduan desain & development (wajib dibaca sebelum ubah tampilan)
```

---

## Database

| Tabel | Isi |
|---|---|
| `users` | Akun admin |
| `clients` | Akun client |
| `categories` | Kategori paket |
| `packages` | Paket (nama, harga, deskripsi, gambar, video) |
| `orders` | Pesanan: relasi ke client & paket, snapshot nama paket & harga saat pesan, bukti bayar, status |
| `planners` | Satu planner per client (pasangan, tanggal, lokasi, anggaran, target tamu) |
| `planner_tasks` | Checklist (judul, catatan, tahap, target, selesai) |
| `planner_budget_items` | Pos anggaran |
| `planner_guests` | Tamu & RSVP |

Menghapus client atau planner otomatis menghapus data turunannya (*cascade*).

**Format deskripsi paket.** Admin menulis satu kalimat ringkasan, lalu fasilitas bernomor. Katalog memecahnya otomatis jadi ringkasan + daftar fasilitas:

```
Dekorasi mewah bertema pastel untuk gedung kecil.
1. Backdrop pelaminan bunga asli
2. Photobooth mini
3. Standing flower 4 pasang
```

---

## Deploy ke Production

1. Upload kode, **tanpa** `vendor/`, `.env`, dan isi `storage/app` (sudah diatur `.gitignore`).
2. **Arahkan document root ke folder `public/`.**
3. Di server:

   ```bash
   composer install --no-dev --optimize-autoloader
   cp .env.example .env          # lalu isi: APP_ENV=production, APP_DEBUG=false, DB_*, APP_URL
   php artisan key:generate
   php artisan migrate --force --seed
   php artisan storage:link
   php artisan media:optimize    # bila memindahkan gambar lama
   php artisan optimize
   ```

4. Pastikan web server boleh menulis ke `storage/` dan `bootstrap/cache/`:

   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

5. Pasang HTTPS (misalnya Let's Encrypt).

**Update versi:** `git pull` → `composer install --no-dev -o` → `php artisan migrate --force` → `php artisan optimize`.

---

## Keamanan

- **Document root wajib menunjuk ke `public/`.** Kalau mengarah ke folder project, file seperti `.env` (password database, `APP_KEY`), log, dan kode bisa diunduh siapa saja. `.htaccess` di root project menolak akses sebagai pengaman cadangan, tapi jangan diandalkan.
- **Jangan commit** `.env`, dump `.sql`, log, kunci SSH, atau isi `storage/app`. Semuanya sudah ada di `.gitignore`. Sebelum push, cek dengan `git status`.
- `APP_DEBUG=false` di production.
- Tidak ada password yang ditulis di kode. Admin pertama dibuat dari `ADMIN_PASSWORD` atau password acak.
- Bukti pembayaran tidak bisa diakses publik, hanya lewat route yang memerlukan login.
- Client hanya bisa melihat dan mengubah data miliknya sendiri (diuji di `PlannerTest`).
- Form memakai CSRF token, dan perubahan data selalu lewat POST/PUT/PATCH/DELETE.
- Login dibatasi 5 percobaan per menit per IP.

Bila `.env` pernah terekspos, **ganti password database** dan jalankan `php artisan key:generate` (semua user akan logout).

---

## Troubleshooting

| Gejala | Solusi |
|---|---|
| Gambar paket tidak muncul | `php artisan storage:link` |
| Upload gagal "file terlalu besar" padahal < 5 MB | Naikkan `upload_max_filesize` & `post_max_size` di `php.ini`, restart web server |
| Video tidak dikompres | Cek `FFMPEG_BINARY` di `.env` mengarah ke file ffmpeg yang benar |
| Error `Too Many Requests (429)` saat login | Tunggu 1 menit (batas 5 percobaan/menit) |
| Perubahan config/route tidak terbaca | `php artisan optimize:clear` |
| Error `Class ... not found` setelah `git pull` | `composer install` lalu `composer dump-autoload` |
| `migrate` gagal di database baru | Pastikan database sudah dibuat dan `DB_*` di `.env` benar |
| Tes gagal "Unknown database takariwo_test" | Buat database tes (lihat [Testing](#testing)) |

---

© Takari Wedding Organizer · Taktakan, Serang – Banten · Instagram [@takari_wo](https://www.instagram.com/takari_wo)
