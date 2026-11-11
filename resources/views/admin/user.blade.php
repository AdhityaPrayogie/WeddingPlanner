@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.sidebar')

        <div class="col-lg-9 col-xl-10 admin-main">
            <header class="page-header">
                <div>
                    <span class="eyebrow">Akun</span>
                    <h1 class="page-title">Kelola Pengguna</h1>
                </div>
            </header>

            @if(session('success') || session('error'))
                <div class="alert alert-{{ session('error') ? 'danger' : 'success' }} alert-dismissible fade show" role="alert">
                    {{ session('error') ?? session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <ul class="nav nav-tabs overflow-auto flex-nowrap" role="tablist">
                <li class="nav-item">
                    <button class="nav-link {{ $active_tab == 'admin' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#admin">Admin</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $active_tab == 'client' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#client">Client</button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Admin Tab --}}
                <div class="tab-pane fade {{ $active_tab == 'admin' ? 'show active' : '' }}" id="admin">
                    <div class="table-responsive table-stack-sm">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th scope="col" class="col-actions">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $index => $row)
                                    <tr>
                                        <td data-label="No." class="tabular text-muted">{{ $admins->firstItem() + $index }}</td>
                                        <td data-label="Username">{{ $row->username }}</td>
                                        <td data-label="Email">{{ $row->email }}</td>
                                        <td class="col-actions">
                                            <div class="table-actions">
                                                <button class="btn btn-outline-secondary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#editAdmin{{ $row->id }}" aria-label="Edit {{ $row->username }}"><i class="bi bi-pencil" aria-hidden="true"></i></button>

                                                <form action="{{ route('admin.users.destroy', ['type' => 'admin', 'id' => $row->id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-icon" onclick="return confirm('Yakin hapus admin ini?')" aria-label="Hapus {{ $row->username }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $admins->links() }}
                </div>

                {{-- Client Tab --}}
                <div class="tab-pane fade {{ $active_tab == 'client' ? 'show active' : '' }}" id="client">
                    <div class="table-responsive table-stack-sm">
                        <table class="table table-hover align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th scope="col" class="col-actions">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $index => $row)
                                    <tr>
                                        <td data-label="No." class="tabular text-muted">{{ $clients->firstItem() + $index }}</td>
                                        <td data-label="Username">{{ $row->username }}</td>
                                        <td data-label="Nama">{{ $row->name }}</td>
                                        <td data-label="Email">{{ $row->email }}</td>
                                        <td data-label="No. HP" class="tabular">{{ $row->phone }}</td>
                                        <td class="col-actions">
                                            <div class="table-actions">
                                            <button class="btn btn-outline-secondary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#editClient{{ $row->id }}" aria-label="Edit {{ $row->username }}"><i class="bi bi-pencil" aria-hidden="true"></i></button>

                                            <form action="{{ route('admin.users.destroy', ['type' => 'client', 'id' => $row->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm btn-icon" onclick="return confirm('Yakin hapus client ini?')" aria-label="Hapus {{ $row->username }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                            </form>
                                        </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $clients->links() }}
                </div>
            </div>

            {{-- Modal Edit Admin --}}
            @foreach($admins as $row)
                <div class="modal fade" id="editAdmin{{ $row->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" action="{{ route('admin.users.admin.update', $row->id) }}" class="modal-content">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Admin</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="text" name="username" class="form-control mb-2" value="{{ $row->username }}" required>
                                <input type="email" name="email" class="form-control mb-2" value="{{ $row->email }}" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Modal Edit Client --}}
            @foreach($clients as $row)
                <div class="modal fade" id="editClient{{ $row->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" action="{{ route('admin.users.client.update', $row->id) }}" class="modal-content">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Client</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="text" name="username" class="form-control mb-2" value="{{ $row->username }}" required>
                                <input type="text" name="name" class="form-control mb-2" value="{{ $row->name }}" required>
                                <input type="email" name="email" class="form-control mb-2" value="{{ $row->email }}" required>
                                <input type="text" name="phone" class="form-control mb-2" value="{{ $row->phone }}" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection
