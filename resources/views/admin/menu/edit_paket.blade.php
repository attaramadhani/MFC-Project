@extends('layouts.admin')

@php
    $title = 'Edit Menu Paket';
    $pageTitle = 'Edit Menu Paket';

    // Buat array map untuk komponen paket saat ini
    $kompMap = [];
    foreach($komposisi as $k) {
        $kompMap[$k->id_menu_komponen] = $k->jumlah;
    }
@endphp

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Edit Menu Paket</h2>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.menu.update', $menu->id_menu) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="is_paket" value="1">
            <input type="hidden" name="harga_beli" value="{{ $menu->harga_beli }}">

            <div class="mb-3">
                <label class="form-label">Nama Paket</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $menu->nama) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select" required>
                    <option value="paket">Paket</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Jual Paket (Otomatis)</label>
                    <input type="number" name="harga" id="harga_jual_paket" class="form-control bg-light" value="{{ old('harga', (int) $menu->harga) }}" readonly>
                    <div class="form-text text-muted">Harga jual dihitung otomatis dari komponen.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" name="diskon" class="form-control" min="0" max="100" value="{{ old('diskon', (int) $menu->diskon) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Stok Paket</label>
                <input type="number" name="stok" class="form-control" value="{{ old('stok', (int) $menu->stok) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar (Kosongkan jika tidak diubah)</label>
                <input type="file" name="gambar" class="form-control">
                @if(!empty($menu->gambar))
                    <div class="mt-2">
                        <img src="{{ get_menu_image_url($menu->gambar) }}" width="100" class="rounded border">
                    </div>
                @endif
            </div>

            <hr class="my-4">

            <h5 class="h6 fw-bold mb-3">Pilih Komponen Penyusun Paket</h5>
            <div class="form-text text-muted mb-3">Centang menu yang termasuk ke dalam paket ini dan atur jumlahnya (Qty). Harga jual paket akan dihitung otomatis dari komponen yang Anda pilih.</div>
            
            <div class="row">
                @foreach($nonPaketMenus as $npm)
                @php
                    $isChecked = isset($kompMap[$npm->id_menu]);
                    $qty = $isChecked ? $kompMap[$npm->id_menu] : 1;
                @endphp
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="border rounded p-2 h-100 d-flex align-items-center gap-2 {{ $isChecked ? 'bg-white border-primary' : 'bg-light' }} komponen-item" data-harga="{{ $npm->harga }}">
                        <div class="form-check m-0">
                            <input class="form-check-input check-komponen" type="checkbox" name="komposisi_id_menu[]" value="{{ $npm->id_menu }}" id="menu_{{ $npm->id_menu }}" {{ $isChecked ? 'checked' : '' }}>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-check-label d-block text-truncate" for="menu_{{ $npm->id_menu }}" style="cursor: pointer;" title="{{ $npm->nama }}">
                                <strong>{{ $npm->nama }}</strong>
                                <br>
                                <small class="text-muted">Rp <span class="harga-komponen-text">{{ number_format($npm->harga, 0, ',', '.') }}</span></small>
                            </label>
                        </div>
                        <div style="width: 70px;">
                            <input type="number" name="komposisi_jumlah[{{ $npm->id_menu }}]" class="form-control form-control-sm qty-input" placeholder="Qty" min="1" value="{{ $qty }}" {{ $isChecked ? '' : 'disabled' }}>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-main text-white">Update Paket</button>
                <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.check-komponen');
    const hargaJualInput = document.getElementById('harga_jual_paket');

    function hitungTotalHarga() {
        let total = 0;
        document.querySelectorAll('.komponen-item').forEach(function(item) {
            const checkbox = item.querySelector('.check-komponen');
            const qtyInput = item.querySelector('.qty-input');
            const harga = parseInt(item.getAttribute('data-harga')) || 0;
            
            if (checkbox.checked) {
                const qty = parseInt(qtyInput.value) || 1;
                total += (harga * qty);
            }
        });
        hargaJualInput.value = total;
    }

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const container = this.closest('.komponen-item');
            const qtyInput = container.querySelector('.qty-input');
            
            if (this.checked) {
                qtyInput.disabled = false;
                container.classList.replace('bg-light', 'bg-white');
                container.classList.add('border-primary');
            } else {
                qtyInput.disabled = true;
                container.classList.replace('bg-white', 'bg-light');
                container.classList.remove('border-primary');
            }
            hitungTotalHarga();
        });
    });

    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('input', hitungTotalHarga);
        input.addEventListener('change', hitungTotalHarga);
    });

    // Calculate initial value
    hitungTotalHarga();
});
</script>
@endsection
