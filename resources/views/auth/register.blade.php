@extends('layouts.app')

@section('content')
<div class="auth-page py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 auth-card">
          <div class="card-body p-4">
            <h3 class="mb-3 text-center section-title">Daftar Akun Baru</h3>

            @if ($errors->any())
              <div class="alert alert-danger py-2">
                {{ $errors->first() }}
              </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
              @csrf

              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="nama_pengguna" class="form-control" value="{{ old('nama_pengguna') }}" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="kata_sandi" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="konfirmasi" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-main text-white w-100">Daftar</button>
            </form>

            <p class="mt-3 small text-center text-muted mb-0">
              Sudah punya akun?
              <a href="{{ route('login') }}">Masuk di sini</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection