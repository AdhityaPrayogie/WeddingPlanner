<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Package;
use App\Support\Media;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Konversi gambar lama (JPG/PNG) ke WebP + versi kecil. Aman dijalankan ulang:
 * file yang sudah .webp dilewati.
 */
class OptimizeMedia extends Command
{
    protected $signature = 'media:optimize';
    protected $description = 'Ubah gambar paket & bukti lama jadi WebP terkompres (+ thumbnail paket)';

    public function handle(): int
    {
        $sebelum = $sesudah = 0;

        foreach (Package::whereNotNull('gambar')->where('gambar', 'not like', '%.webp')->get() as $p) {
            [$p->gambar, $a, $b] = $this->convert($p->gambar, 'uploads', 'public', 640);
            $p->save();
            $sebelum += $a; $sesudah += $b;
        }

        foreach (Order::whereNotNull('bukti_pembayaran')->where('bukti_pembayaran', 'not like', '%.webp')->get() as $o) {
            [$o->bukti_pembayaran, $a, $b] = $this->convert($o->bukti_pembayaran, 'bukti', 'local', null);
            $o->save();
            $sebelum += $a; $sesudah += $b;
        }

        // Gambar statis landing page: buat pasangan .webp di sebelah file asli
        foreach (glob(public_path('asset/img/img-*.jpg')) as $jpg) {
            $tmp = new UploadedFile($jpg, basename($jpg), null, null, true);
            foreach ([1600 => '', 800 => '-sm'] as $w => $suffix) {
                $name = Media::storeImage($tmp, 'tmp-static', 'local', $w);
                $target = preg_replace('/\.jpg$/', "$suffix.webp", $jpg);
                rename(Storage::disk('local')->path("tmp-static/$name"), $target);
                $sesudah += filesize($target);
            }
        }
        Storage::disk('local')->deleteDirectory('tmp-static');

        $this->info(sprintf('Selesai. %.1f MB -> %.1f MB', $sebelum / 1e6, $sesudah / 1e6));

        return self::SUCCESS;
    }

    private function convert(string $name, string $dir, string $disk, ?int $thumb): array
    {
        $path = Storage::disk($disk)->path("$dir/$name");
        if (!is_file($path)) {
            $this->warn("Lewati, file tidak ada: $dir/$name");
            return [$name, 0, 0];
        }

        $baru = Media::storeImage(new UploadedFile($path, $name, null, null, true), $dir, $disk, 1600, $thumb);
        $ukuranLama = filesize($path);
        $ukuranBaru = Storage::disk($disk)->size("$dir/$baru");
        Storage::disk($disk)->delete("$dir/$name");
        $this->line("$dir/$name -> $baru");

        return [$baru, $ukuranLama, $ukuranBaru];
    }
}
