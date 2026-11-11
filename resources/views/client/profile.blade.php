@extends('layouts.main')

@section('title', 'Profil Saya | Takari')

@section('content')
<div class="container section">
    <div class="page-narrow">
        <header class="page-header">
            <div>
                <span class="eyebrow">Akun</span>
                <h1 class="page-title">Profil Saya</h1>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('client.profile.update') }}" class="panel">
            @csrf
            @method('PUT')

            @foreach ([
                ['name', 'Nama', 'text', 'name'],
                ['email', 'Email', 'email', 'email'],
                ['phone', 'No. HP', 'tel', 'tel'],
            ] as [$field, $label, $type, $auto])
                <div class="mb-4">
                    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                    <input type="{{ $type }}" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $user->$field) }}" autocomplete="{{ $auto }}"
                        class="form-control @error($field) is-invalid @enderror" required
                        @error($field) aria-describedby="{{ $field }}-error" @enderror>
                    @error($field) <div class="invalid-feedback" id="{{ $field }}-error">{{ $message }}</div> @enderror
                </div>
            @endforeach

            <div class="d-flex justify-content-between gap-2 mt-5">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
