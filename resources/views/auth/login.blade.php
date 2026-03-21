@extends('layouts.app')

@section('content')
<div class="auth-page">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4 auth-card">
          <div class="card-body p-4 p-md-4">
            <h3 class="mb-3 text-center section-title">Masuk</h3>

            @if ($errors->any())
              <div class="alert alert-danger py-2">
                {{ $errors->first() }}
              </div>
            @endif

            <form method="POST" action="{{ route('login.process') }}">
              @csrf
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="nama_pengguna" class="form-control" value="{{ old('nama_pengguna') }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="kata_sandi" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-main text-white w-100">Masuk</button>
            </form>

            <p class="mt-3 small text-center text-muted">
              Belum punya akun?
              <a href="{{ route('register') }}">Daftar disini</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>