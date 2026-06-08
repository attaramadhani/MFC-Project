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
                <label class="form-label">Tipe Menu</label>
                <select name="is_paket" class="form-select" required>
                    <option value="0">Menu Biasa / Satuan</option>
                    <option value="1">Menu Paket</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="paket">Paket</option>
                    <option value="tambahan">Tambahan</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli', 0) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Jual</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="{{ old('stok', 0) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" name="diskon" class="form-control" min="0" max="100" value="{{ old('diskon', 0) }}">
                    <div class="form-text text-muted">Bisa dikosongkan atau diisi 0 jika tidak ada diskon.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar</label>
                <input type="file" name="gambar" class="form-control">
            </div>

            <!-- KOMPOSISI PAKET -->
            <div id="komposisi-section" class="mb-4 border p-3 rounded bg-light" style="display: none;">
                <h5 class="h6 fw-bold mb-3">Komposisi Paket</h5>
                <div id="komposisi-container">
                    <div class="row g-2 mb-2 komposisi-row">
                        <div class="col-md-8">
                            <select name="komposisi_id_menu[]" class="form-select">
                                <option value="">Pilih Komponen</option>
                                @foreach($nonPaketMenus as $npm)
                                    <option value="{{ $npm->id_menu }}">{{ $npm->nama }} - Rp {{ number_format($npm->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="komposisi_jumlah[]" class="form-control" placeholder="Qty" value="1" min="1">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-add-komposisi">+ Tambah Komponen</button>
                <div class="form-text text-muted mt-2">Pilih menu satuan yang termasuk ke dalam paket ini.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-main text-white">Simpan</button>
                <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isPaketSelect = document.querySelector('select[name="is_paket"]');
    const komposisiSection = document.getElementById('komposisi-section');
    const btnAddKomposisi = document.getElementById('btn-add-komposisi');
    const komposisiContainer = document.getElementById('komposisi-container');

    function toggleKomposisi() {
        if (isPaketSelect.value === '1') {
            komposisiSection.style.display = 'block';
        } else {
            komposisiSection.style.display = 'none';
        }
    }

    isPaketSelect.addEventListener('change', toggleKomposisi);
    toggleKomposisi();

    btnAddKomposisi.addEventListener('click', function() {
        const firstRow = komposisiContainer.querySelector('.komposisi-row');
        if (firstRow) {
            const newRow = firstRow.cloneNode(true);
            newRow.querySelector('select').value = '';
            newRow.querySelector('input').value = '1';
            komposisiContainer.appendChild(newRow);
        }
    });

    komposisiContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const rows = komposisiContainer.querySelectorAll('.komposisi-row');
            if (rows.length > 1) {
                e.target.closest('.komposisi-row').remove();
            } else {
                alert('Minimal harus ada 1 baris komponen.');
            }
        }
    });
});
</script>
@endsection