@extends('layouts.app')

@section('content')
<div class="auth-page">
    {{-- LEFT: Branding --}}
    <div class="auth-split-left d-none d-md-flex">
        <div class="auth-brand-logo mb-3">
            <img src="{{ asset('img/logo.jpg') }}" alt="MFC Logo" width="140" class="rounded-4 shadow-lg" style="object-fit: cover;">
        </div>
        <div class="auth-brand-name">MFC</div>
        <div class="auth-brand-tagline">
            Madris Fried Chicken<br>
            Bergabung sekarang dan nikmati<br>kemudahan pesan online.
        </div>
        <div class="mt-4" style="font-size:0.8rem; opacity:0.5;">Gratis • Mudah • Cepat</div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="auth-split-right">
        <div class="auth-card">
            <div class="auth-card-title">Buat Akun Baru ✨</div>
            <div class="auth-card-sub">Daftar dan mulai pesan favoritmu</div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 rounded-3 mb-3" style="font-size:0.85rem;">
                    <strong>⚠️</strong> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="reg-username">Username</label>
                    <input
                        type="text"
                        id="reg-username"
                        name="nama_pengguna"
                        class="form-control"
                        value="{{ old('nama_pengguna') }}"
                        placeholder="Buat username unik kamu"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label" for="reg-password">Password</label>
                    <div class="pw-wrap">
                        <input
                            type="password"
                            id="reg-password"
                            name="kata_sandi"
                            class="form-control"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="pw-toggle" onclick="togglePw('reg-password', this)" tabindex="-1" aria-label="Tampilkan password">
                            <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="reg-confirm">Konfirmasi Password</label>
                    <div class="pw-wrap">
                        <input
                            type="password"
                            id="reg-confirm"
                            name="konfirmasi"
                            class="form-control"
                            placeholder="Ulangi password kamu"
                            autocomplete="new-password"
                            required
                        >
                        <button type="button" class="pw-toggle" onclick="togglePw('reg-confirm', this)" tabindex="-1" aria-label="Tampilkan password">
                            <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btn-register" class="btn btn-main w-100">
                    Daftar Sekarang
                </button>
            </form>

            <div class="auth-divider">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.eye-icon').style.display     = isHidden ? 'none' : '';
    btn.querySelector('.eye-off-icon').style.display = isHidden ? ''     : 'none';
    btn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
}

document.addEventListener('DOMContentLoaded', function() {
    const pwInput = document.getElementById('reg-password');
    const pwConfirm = document.getElementById('reg-confirm');

    function checkPassword() {
        if (pwInput.value.length >= 8) {
            pwInput.classList.add('pw-valid');
        } else {
            pwInput.classList.remove('pw-valid');
        }
        
        if (pwConfirm.value.length >= 8 && pwConfirm.value === pwInput.value) {
            pwConfirm.classList.add('pw-valid');
        } else {
            pwConfirm.classList.remove('pw-valid');
        }
    }

    if (pwInput && pwConfirm) {
        pwInput.addEventListener('input', checkPassword);
        pwConfirm.addEventListener('input', checkPassword);
    }
});
</script>
@endpush
@endsection