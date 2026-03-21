@extends('layouts.admin')

@php
    $title = 'Tambah Menu';
    $pageTitle = 'Tambah Menu';
@endphp

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Tambah Menu</h2>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="tambahan">Tambahan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar</label>
                <input type="file" name="gambar" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-main text-white">Simpan</button>
                <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection