@extends('layouts.auth')

@section('title', 'Masuk Admin')
@section('eyebrow', 'Panel Admin')

@section('content')
<form method="POST" action="{{ route('admin.login.submit') }}">
    @csrf

    <div class="mb-4">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" value="{{ old('username') }}" class="form-control"
            autocomplete="username" required autofocus>
    </div>

    <div class="mb-5">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
            <button class="btn btn-outline-secondary" type="button" data-toggle-password="password" aria-label="Tampilkan password" aria-pressed="false">
                <i class="bi bi-eye-slash"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Masuk</button>
</form>
@endsection
