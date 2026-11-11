<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $kategoriData = Category::all();
        $kategori_filter = $request->input('kategori', '');

        $data = Package::with('category')
            ->when($kategori_filter, fn ($q) => $q->where('category_id', (int) $kategori_filter))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.paket', compact('kategoriData', 'data', 'kategori_filter'));
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request, true);

        $validated['gambar'] = Media::storeImage($request->file('gambar'), 'uploads', thumbWidth: 640);
        if ($request->hasFile('video')) {
            $validated['video'] = Media::storeVideo($request->file('video'), 'videos');
        }

        Package::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        $validated = $this->validasi($request, false);
        unset($validated['gambar'], $validated['video'], $validated['hapus_video']);

        if ($request->hasFile('gambar')) {
            Media::deleteImage($package->gambar, 'uploads');
            $validated['gambar'] = Media::storeImage($request->file('gambar'), 'uploads', thumbWidth: 640);
        }

        if ($request->hasFile('video') || $request->boolean('hapus_video')) {
            $this->hapusVideo($package);
            $validated['video'] = $request->hasFile('video')
                ? Media::storeVideo($request->file('video'), 'videos')
                : null;
        }

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        Media::deleteImage($package->gambar, 'uploads');
        $this->hapusVideo($package);
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil dihapus!');
    }

    private function validasi(Request $request, bool $baru): array
    {
        return $request->validate([
            'nama'        => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'deskripsi'   => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'gambar'      => Media::imageRule($baru),
            'video'       => Media::videoRule(),
            'hapus_video' => 'nullable|boolean',
        ], Media::messages('gambar', 'Gambar') + Media::messages('video', 'Video'));
    }

    private function hapusVideo(Package $package): void
    {
        if ($package->video) {
            Storage::disk('public')->delete('videos/' . $package->video);
        }
    }
}
