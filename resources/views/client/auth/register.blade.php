@extends('layouts.auth')

@section('title', 'Buat Akun')
@section('eyebrow', 'Mulai Bersama Takari')

@section('content')
<form method="POST" action="{{ route('client.register.submit') }}">
    @csrf

    @foreach ([
        ['name', 'Nama Lengkap', 'text', 'name'],
        ['email', 'Email', 'email', 'email'],
        ['username', 'Username', 'text', 'username'],
        ['phone', 'No. HP', 'tel', 'tel'],
    ] as [$field, $label, $type, $auto])
        <div class="mb-4">
            <label for="{{ $field }}" class="form-label">{{ $label }}</label>
            <input type="{{ $type }}" name="{{ $field }}" id="{{ $field }}" value="{{ old($field) }}" autocomplete="{{ $auto }}"
                class="form-control @error($field) is-invalid @enderror" required
                @error($field) aria-describedby="{{ $field }}-error" @enderror>
            @error($field) <div class="invalid-feedback" id="{{ $field }}-error">{{ $message }}</div> @enderror
        </div>
    @endforeach

    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" autocomplete="new-password"
            class="form-control @error('password') is-invalid @enderror" required
            @error('password') aria-describedby="password-error" @enderror>
        @error('password') <div class="invalid-feedback" id="password-error">{{ $message }}</div> @enderror
    </div>

    <div class="mb-5">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Daftar</button>

    <p class="auth-foot">
        Sudah punya akun? <a href="{{ route('client.login') }}">Masuk</a>
    </p>
</form>
@endsection
