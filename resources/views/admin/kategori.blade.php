@extends('layouts.main')

@section('title', 'Kelola Kategori | TakariWO')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main">
            <header class="page-header">
                <div>
                    <span class="eyebrow">Katalog</span>
                    <h1 class="page-title">Kelola Kategori</h1>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="toolbar">
                <h2>Daftar Kategori</h2>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah
                </button>
            </div>
            <div class="modal fade" id="modalTambah" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="modal-content shadow">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label for="nama-baru" class="form-label">Nama Kategori</label>
                            <input type="text" name="nama" id="nama-baru" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive table-stack-sm">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Nama Kategori</th>
                            <th scope="col" class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td data-label="Nama Kategori">{{ $category->nama }}</td>
                                <td class="col-actions"><div class="table-actions">
                                    <button class="btn btn-sm btn-outline-secondary btn-icon" aria-label="Edit {{ $category->nama }}" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-icon" aria-label="Hapus {{ $category->nama }}" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @foreach($categories as $category)
            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="modal-content shadow">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="nama" value="{{ $category->nama }}" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
