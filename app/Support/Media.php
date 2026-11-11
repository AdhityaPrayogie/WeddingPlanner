<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Upload + kompres media. Satu tempat untuk batas ukuran dan format.
 */
class Media
{
    public const IMAGE_MAX_KB = 5120;   // 5 MB
    public const VIDEO_MAX_KB = 10240;  // 10 MB
    public const IMAGE_MIMES = 'jpeg,jpg,png,webp';
    public const VIDEO_MIMES = 'mp4,webm,mov';

    public static function imageRule(bool $required = true): string
    {
        return ($required ? 'required' : 'nullable') . '|image|mimes:' . self::IMAGE_MIMES . '|max:' . self::IMAGE_MAX_KB;
    }

    public static function videoRule(): string
    {
        return 'nullable|file|mimes:' . self::VIDEO_MIMES . '|max:' . self::VIDEO_MAX_KB;
    }

    public static function messages(string $field, string $label): array
    {
        return [
            "$field.max"   => "$label maksimal " . ($field === 'video' ? '10' : '5') . ' MB.',
            "$field.mimes" => "Format $label harus " . ($field === 'video' ? 'MP4, WebM, atau MOV' : 'JPG, PNG, atau WebP') . '.',
            "$field.image" => "$label harus berupa gambar.",
        ];
    }

    /**
     * Simpan gambar sebagai WebP (lebar maks. $maxWidth, putaran EXIF diperbaiki).
     * Bila $thumbWidth diisi, juga dibuat "<nama>-sm.webp" untuk layar kecil.
     * Mengembalikan nama file (tanpa folder).
     */
    public static function storeImage(UploadedFile $file, string $dir, string $disk = 'public', int $maxWidth = 1600, ?int $thumbWidth = null): string
    {
        $name = Str::random(32) . '.webp';
        $src = self::loadImage($file->getRealPath());

        if (!$src) {
            // GD tidak bisa membaca: simpan apa adanya (sudah lolos validasi mime & ukuran)
            return basename($file->store($dir, $disk));
        }

        Storage::disk($disk)->put("$dir/$name", self::encodeWebp($src, $maxWidth));
        if ($thumbWidth) {
            Storage::disk($disk)->put("$dir/" . self::thumbName($name), self::encodeWebp($src, $thumbWidth));
        }
        imagedestroy($src);

        return $name;
    }

    public static function thumbName(string $name): string
    {
        return preg_replace('/\.webp$/', '-sm.webp', $name);
    }

    public static function deleteImage(?string $name, string $dir, string $disk = 'public'): void
    {
        if ($name) {
            Storage::disk($disk)->delete(["$dir/$name", "$dir/" . self::thumbName($name)]);
        }
    }

    /**
     * Simpan video. Dikompres ke MP4 H.264 720p bila ffmpeg tersedia dan hasilnya lebih kecil.
     * ponytail: kompres sinkron saat request; pindah ke queue bila upload video jadi lambat.
     */
    public static function storeVideo(UploadedFile $file, string $dir, string $disk = 'public'): string
    {
        $out = tempnam(sys_get_temp_dir(), 'vid') . '.mp4';
        $process = new Process([
            config('services.ffmpeg.binary', 'ffmpeg'), '-y', '-i', $file->getRealPath(),
            '-vf', 'scale=min(1280\,iw):-2',
            '-c:v', 'libx264', '-crf', '28', '-preset', 'veryfast', '-pix_fmt', 'yuv420p',
            '-c:a', 'aac', '-b:a', '96k',
            '-movflags', '+faststart',
            $out,
        ]);
        $process->setTimeout(120);

        try {
            $process->run();
        } catch (\Throwable $e) {
            report($e); // ffmpeg tidak ada / gagal jalan: pakai file asli
        }

        if ($process->isSuccessful() && is_file($out) && filesize($out) > 0 && filesize($out) < $file->getSize()) {
            $name = Str::random(32) . '.mp4';
            Storage::disk($disk)->putFileAs($dir, new \Illuminate\Http\File($out), $name);
        } else {
            $name = basename($file->store($dir, $disk));
        }
        @unlink($out);

        return $name;
    }

    private static function loadImage(string $path): ?\GdImage
    {
        // foto HP 24MP butuh ±100MB RAM di GD
        ini_set('memory_limit', '512M');

        $type = @getimagesize($path)[2] ?? null;
        $img = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default        => false,
        };
        if (!$img) {
            return null;
        }

        // Foto HP sering tersimpan miring + tag EXIF; re-encode membuang EXIF, jadi putar dulu.
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $angle = [3 => 180, 6 => -90, 8 => 90][@exif_read_data($path)['Orientation'] ?? 1] ?? 0;
            if ($angle) {
                $rotated = imagerotate($img, $angle, 0);
                imagedestroy($img);
                $img = $rotated;
            }
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        return $img;
    }

    private static function encodeWebp(\GdImage $src, int $maxWidth): string
    {
        $img = imagesx($src) > $maxWidth ? imagescale($src, $maxWidth, -1, IMG_BICUBIC) : $src;
        imagesavealpha($img, true);

        ob_start();
        imagewebp($img, null, 78);
        $data = ob_get_clean();

        if ($img !== $src) {
            imagedestroy($img);
        }

        return $data;
    }
}
