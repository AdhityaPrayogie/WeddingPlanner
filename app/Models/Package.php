<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'deskripsi',
        'category_id',
        'gambar',
        'video',
    ];

    /** URL gambar penuh & versi kecil (fallback ke penuh untuk upload lama tanpa versi kecil). */
    public function getGambarUrlAttribute(): string
    {
        return asset('storage/uploads/' . $this->gambar);
    }

    public function getGambarKecilUrlAttribute(): string
    {
        $kecil = \App\Support\Media::thumbName((string) $this->gambar);

        return $kecil !== $this->gambar && is_file(storage_path('app/public/uploads/' . $kecil))
            ? asset('storage/uploads/' . $kecil)
            : $this->gambar_url;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video ? asset('storage/videos/' . $this->video) : null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Pecah deskripsi jadi ['ringkasan' => string, 'fitur' => string[]].
     * Baris berawalan "1." / "2)" dianggap fitur, sisanya ringkasan.
     */
    public function getRincianAttribute(): array
    {
        $ringkasan = [];
        $fitur = [];

        foreach (preg_split('/\R/', (string) $this->deskripsi) as $baris) {
            $baris = trim($baris);
            if ($baris === '') {
                continue;
            }
            if (preg_match('/^\d+[.)]\s*(.+)$/u', $baris, $m)) {
                $fitur[] = trim($m[1]);
            } else {
                $ringkasan[] = $baris;
            }
        }

        return ['ringkasan' => implode(' ', $ringkasan), 'fitur' => $fitur];
    }
}
