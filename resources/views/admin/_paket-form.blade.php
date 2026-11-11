{{-- Form paket dipakai modal Tambah ($row = null) dan Edit ($row = Package). --}}
@php
    $id = $row ? 'edit' . $row->id : 'modalTambah';
    $isThis = old('_modal') === $id; // old() hanya diisi untuk modal yang barusan gagal validasi
    $val = fn ($field) => $isThis ? old($field) : ($row->$field ?? null);
@endphp
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
        <form method="POST" action="{{ $row ? route('admin.packages.update', $row->id) : route('admin.packages.store') }}"
            enctype="multipart/form-data" class="modal-content">
            @csrf
            @if ($row) @method('PUT') @endif
            <input type="hidden" name="_modal" value="{{ $id }}">

            <div class="modal-header">
                <h2 class="modal-title" id="{{ $id }}Label">{{ $row ? 'Edit Paket' : 'Tambah Paket' }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                @if ($isThis && $errors->any())
                    <div class="alert alert-danger" role="alert">Periksa kembali isian yang ditandai.</div>
                @endif

                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="{{ $id }}-nama" class="form-label">Nama Paket</label>
                        <input type="text" name="nama" id="{{ $id }}-nama" value="{{ $val('nama') }}" maxlength="255" required
                            class="form-control {{ $isThis && $errors->has('nama') ? 'is-invalid' : '' }}">
                        @if ($isThis) @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
                    </div>
                    <div class="col-md-5">
                        <label for="{{ $id }}-kategori" class="form-label">Kategori</label>
                        <select name="category_id" id="{{ $id }}-kategori" required
                            class="form-select {{ $isThis && $errors->has('category_id') ? 'is-invalid' : '' }}">
                            <option value="">Pilih kategori</option>
                            @foreach ($kategoriData as $kategori)
                                <option value="{{ $kategori->id }}" @selected($val('category_id') == $kategori->id)>{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                        @if ($isThis) @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
                    </div>
                    <div class="col-12">
                        <label for="{{ $id }}-harga" class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" id="{{ $id }}-harga" value="{{ $val('harga') !== null ? (int) $val('harga') : '' }}"
                            min="0" step="1" inputmode="numeric" required
                            class="form-control {{ $isThis && $errors->has('harga') ? 'is-invalid' : '' }}">
                        @if ($isThis) @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
                    </div>
                    <div class="col-12">
                        <label for="{{ $id }}-deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="{{ $id }}-deskripsi" rows="6" required aria-describedby="{{ $id }}-deskripsi-help"
                            class="form-control {{ $isThis && $errors->has('deskripsi') ? 'is-invalid' : '' }}">{{ $val('deskripsi') }}</textarea>
                        <div id="{{ $id }}-deskripsi-help" class="form-text">Satu kalimat ringkasan, lalu fasilitas bernomor: 1. … 2. …</div>
                        @if ($isThis) @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
                    </div>

                    <div class="col-md-6">
                        <label for="{{ $id }}-gambar" class="form-label">
                            Gambar @if ($row) <span class="optional">(kosongkan bila tidak diganti)</span> @endif
                        </label>
                        @if ($row && $row->gambar)
                            <img src="{{ $row->gambar_kecil_url }}" alt="Gambar saat ini" class="thumb thumb-lg d-block mb-2" width="80" height="100" loading="lazy">
                        @endif
                        <input type="file" name="gambar" id="{{ $id }}-gambar" accept="image/jpeg,image/png,image/webp"
                            data-max-mb="5" aria-describedby="{{ $id }}-gambar-help" @if (!$row) required @endif
                            class="form-control {{ $isThis && $errors->has('gambar') ? 'is-invalid' : '' }}">
                        <div id="{{ $id }}-gambar-help" class="form-text">JPG, PNG, atau WebP. Maks. 5 MB. Otomatis dikompres.</div>
                        @if ($isThis) @error('gambar') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
                    </div>

                    <div class="col-md-6">
                        <label for="{{ $id }}-video" class="form-label">Video <span class="optional">(opsional)</span></label>
                        @if ($row && $row->video)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="hapus_video" value="1" id="{{ $id }}-hapus-video">
                                <label class="form-check-label small" for="{{ $id }}-hapus-video">Hapus video saat ini</label>
                            </div>
                        @endif
                        <input type="file" name="video" id="{{ $id }}-video" accept="video/mp4,video/webm,video/quicktime"
                            data-max-mb="10" aria-describedby="{{ $id }}-video-help"
                            class="form-control {{ $isThis && $errors->has('video') ? 'is-invalid' : '' }}">
                        <div id="{{ $id }}-video-help" class="form-text">MP4, WebM, atau MOV. Maks. 10 MB. Otomatis dikompres ke 720p.</div>
                        @if ($isThis) @error('video') <div class="invalid-feedback">{{ $message }}</div> @enderror @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">{{ $row ? 'Simpan Perubahan' : 'Simpan' }}</button>
            </div>
        </form>
    </div>
</div>
