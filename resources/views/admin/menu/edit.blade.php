@extends('layouts.admin')

@php
    $title = 'Edit Menu';
    $pageTitle = 'Edit Menu';
@endphp

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Edit Menu</h2>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.menu.update', $menu->id_menu) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $menu->nama) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipe Menu</label>
                <select name="is_paket" class="form-select" required>
                    <option value="0" {{ old('is_paket', $menu->is_paket) == 0 ? 'selected' : '' }}>Menu Biasa / Satuan</option>
                    <option value="1" {{ old('is_paket', $menu->is_paket) == 1 ? 'selected' : '' }}>Menu Paket</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="makanan" {{ old('kategori', $menu->kategori) === 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ old('kategori', $menu->kategori) === 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="paket" {{ old('kategori', $menu->kategori) === 'paket' ? 'selected' : '' }}>Paket</option>
                    <option value="tambahan" {{ old('kategori', $menu->kategori) === 'tambahan' ? 'selected' : '' }}>Tambahan</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli', (int)$menu->harga_beli) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Jual</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga', (int)$menu->harga) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="{{ old('stok', (int)$menu->stok) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" name="diskon" class="form-control" min="0" max="100" value="{{ old('diskon', (int)$menu->diskon) }}">
                    <div class="form-text text-muted">Bisa dikosongkan atau diisi 0 jika tidak ada diskon.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar Lama</label><br>
                @if(!empty($menu->gambar))
                    <img src="{{ asset('img/' . $menu->gambar) }}" width="80" class="rounded" alt="{{ $menu->nama }}">
                @else
                    <span class="text-muted small">Belum ada gambar</span>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">Ganti Gambar</label>
                <input type="file" name="gambar" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-main text-white">Update</button>
                <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection