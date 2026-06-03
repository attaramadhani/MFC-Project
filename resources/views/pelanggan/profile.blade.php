@extends('layouts.app')

@section('content')
    @include('layouts.navbar')

    <div class="container page-orders" style="min-height: calc(100vh - 70px);">
        <div class="mb-4">
            <h1 class="page-orders-title">Profil Saya</h1>
            <p class="page-orders-subtitle">Atur informasi profil dan password akun MFC kamu.</p>
        </div>

        <div class="row g-4 mb-5">
            {{-- Bagian Update Profil --}}
            <div class="col-md-6">
                <div class="card order-card h-100 flex-column align-items-stretch" style="cursor: default;">
                    <div class="mb-3 border-bottom pb-2">
                        <h5 class="fw-bold text-dark mb-0">Informasi Pribadi</h5>
                    </div>


                    @if ($errors->any() && !old('password_lama'))
                        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pelanggan.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Username / Nama Lengkap</label>
                            <input type="text" name="nama_user" class="form-control" value="{{ old('nama_user', $user->nama_user) }}" required>
                        </div>

                        <button type="submit" class="btn btn-main w-100">Simpan Profil</button>
                    </form>
                </div>
            </div>

            {{-- Bagian Update Password --}}
            <div class="col-md-6">
                <div class="card order-card h-100 flex-column align-items-stretch" style="cursor: default;">
                    <div class="mb-3 border-bottom pb-2">
                        <h5 class="fw-bold text-dark mb-0">Keamanan (Ganti Password)</h5>
                    </div>



                    @if ($errors->any() && old('password_lama'))
                        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pelanggan.profile.password') }}">
                        @csrf
                        <input type="hidden" name="password_update" value="1">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Konfirmasi Password Baru</label>
                            <input type="password" name="password_konfirmasi" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-outline-secondary rounded-pill w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
