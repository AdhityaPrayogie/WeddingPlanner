@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main">
            <header class="page-header">
                <div>
                    <span class="eyebrow">Katalog</span>
                    <h1 class="page-title">Manajemen Paket</h1>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="toolbar">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Paket
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel" aria-hidden="true"></i> Kategori
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.packages.index') }}">Semua</a></li>
                        @foreach($kategoriData as $kategori)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.packages.index', ['kategori' => $kategori->id]) }}">
                                    {{ $kategori->nama }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Tabel Paket --}}
            <div class="table-responsive table-stack-sm">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Gambar</th>
                            <th scope="col">Nama</th>
                            <th scope="col" class="text-end">Harga</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                        <tr>
                            <td data-label="Gambar">
                                @if($row->gambar)
                                    <img src="{{ $row->gambar_kecil_url }}" class="thumb thumb-lg" width="80" height="100" alt="{{ $row->nama }}" loading="lazy" decoding="async">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td data-label="Nama" class="fw-semibold">
                                {{ ucwords($row->nama) }}
                                @if ($row->video) <i class="bi bi-camera-video text-muted ms-1" title="Ada video" aria-label="Ada video"></i> @endif
                            </td>
                            <td data-label="Harga" class="tabular text-nowrap text-end">Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
                            <td data-label="Deskripsi" class="text-muted"><div class="cell-clamp">{{ $row->rincian['ringkasan'] ?: $row->deskripsi }}</div></td>
                            <td data-label="Kategori">{{ $row->category->nama ?? '-' }}</td>
                            <td class="col-actions"><div class="table-actions">
                                <button class="btn btn-outline-secondary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#edit{{ $row->id }}" aria-label="Edit {{ $row->nama }}">
                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                </button>
                                <form action="{{ route('admin.packages.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus paket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-icon" aria-label="Hapus {{ $row->nama }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                </form>
                            </div></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Belum ada paket.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $data->links() }}

            @include('admin._paket-form', ['row' => null])
            @foreach ($data as $row)
                @include('admin._paket-form', ['row' => $row])
            @endforeach
        </div>
    </div>
</div>

@if (old('_modal'))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById(@json(old('_modal')));
            if (el) new bootstrap.Modal(el).show();
        });
    </script>
    @endpush
@endif
@endsection
