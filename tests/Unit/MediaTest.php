<?php

namespace Tests\Unit;

use App\Support\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MediaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_gambar_besar_dikecilkan_jadi_webp_dengan_thumbnail(): void
    {
        $file = UploadedFile::fake()->image('foto.jpg', 4000, 3000);

        $name = Media::storeImage($file, 'uploads', thumbWidth: 640);

        $this->assertStringEndsWith('.webp', $name);
        [$w, $h] = getimagesize(Storage::disk('public')->path("uploads/$name"));
        $this->assertSame([1600, 1200], [$w, $h]);
        [$tw] = getimagesize(Storage::disk('public')->path('uploads/' . Media::thumbName($name)));
        $this->assertSame(640, $tw);

        Media::deleteImage($name, 'uploads');
        Storage::disk('public')->assertMissing(["uploads/$name", 'uploads/' . Media::thumbName($name)]);
    }

    public function test_gambar_kecil_tidak_diperbesar(): void
    {
        $name = Media::storeImage(UploadedFile::fake()->image('kecil.png', 300, 200), 'uploads');

        $this->assertSame(300, getimagesize(Storage::disk('public')->path("uploads/$name"))[0]);
    }

    public function test_batas_ukuran_gambar_5mb_dan_video_10mb(): void
    {
        $cek = fn ($field, $rule, $file) => Validator::make([$field => $file], [$field => $rule])->passes();

        $this->assertTrue($cek('g', Media::imageRule(), UploadedFile::fake()->image('ok.jpg')->size(5120)));
        $this->assertFalse($cek('g', Media::imageRule(), UploadedFile::fake()->image('besar.jpg')->size(5121)));
        $this->assertFalse($cek('g', Media::imageRule(), UploadedFile::fake()->create('x.gif', 10, 'image/gif')));

        $this->assertTrue($cek('v', Media::videoRule(), UploadedFile::fake()->create('ok.mp4', 10240, 'video/mp4')));
        $this->assertFalse($cek('v', Media::videoRule(), UploadedFile::fake()->create('besar.mp4', 10241, 'video/mp4')));
        $this->assertFalse($cek('v', Media::videoRule(), UploadedFile::fake()->create('x.avi', 10, 'video/x-msvideo')));
    }

    public function test_video_dikompres_ffmpeg_bila_tersedia(): void
    {
        $ffmpeg = config('services.ffmpeg.binary');
        if (!is_file($ffmpeg)) {
            $this->markTestSkipped('ffmpeg tidak tersedia');
        }

        // Video uji 1080p 3 detik, bitrate tinggi
        $src = sys_get_temp_dir() . '/media-test-src.mp4';
        exec(escapeshellarg($ffmpeg) . ' -y -loglevel error -f lavfi -i testsrc2=size=1920x1080:rate=30 -t 3 -c:v libx264 -crf 10 ' . escapeshellarg($src));
        $file = new UploadedFile($src, 'video.mp4', 'video/mp4', null, true);

        $name = Media::storeVideo($file, 'videos');
        $out = Storage::disk('public')->path("videos/$name");

        $this->assertLessThan(filesize($src), filesize($out));
        $this->assertStringContainsString('1280x720', shell_exec(escapeshellarg($ffmpeg) . ' -i ' . escapeshellarg($out) . ' 2>&1'));
        @unlink($src);
    }

    public function test_video_tetap_tersimpan_bila_ffmpeg_tidak_ada(): void
    {
        config(['services.ffmpeg.binary' => 'ffmpeg-tidak-ada-xyz']);
        $name = Media::storeVideo(UploadedFile::fake()->create('v.mp4', 100, 'video/mp4'), 'videos');

        Storage::disk('public')->assertExists("videos/$name");
    }
}
