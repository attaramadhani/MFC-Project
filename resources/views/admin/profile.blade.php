@extends('layouts.admin')

@php
$title = 'Profil Admin';
$pageTitle = 'Profil Admin';
@endphp

@section('content')

<div class="row">

<div class="col-md-6">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">

            <h5 class="mb-3">Ganti Password</h5>

            @if(session('success'))
                <div class="alert alert-success py-2">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger py-2">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf

                <div class="mb-3">
                    <label>Password Lama</label>
                    <input 
                        type="password"
                        name="password_lama"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label>Password Baru</label>
                    <input 
                        type="password"
                        name="password_baru"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password</label>
                    <input 
                        type="password"
                        name="password_konfirmasi"
                        class="form-control"
                        required
                    >
                </div>

                <button class="btn btn-main text-white">
                    Simpan
                </button>

            </form>

        </div>

    </div>

</div>

</div>

@endsection
