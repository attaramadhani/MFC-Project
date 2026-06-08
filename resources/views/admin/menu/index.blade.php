@extends('layouts.admin')

@php
    $pageTitle = 'Kelola Menu';
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h5 mb-1">Kelola Menu</h2>
        <div class="text-muted small">
            Tambah, edit, dan hapus menu yang tersedia untuk pelanggan.
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.menu.create') }}" class="btn btn-main text-white">
            + Tambah Menu
        </a>
        <a href="{{ route('admin.menu.paket.create') }}" class="btn btn-warning text-dark">
            + Tambah Paket
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <form action="{{ route('admin.menu.bulk_destroy') }}" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua menu yang dipilih?')">
            @csrf
        </form>
        <div class="mb-3">
            <button type="submit" form="bulkDeleteForm" class="btn btn-danger btn-sm" id="btnBulkDelete" disabled>
                Hapus yang Dipilih (<span id="selectedCount">0</span>)
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                        </th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Diskon</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $m)
                        <tr>
                            <td>
                                <input class="form-check-input menu-checkbox" type="checkbox" name="menu_ids[]" value="{{ $m->id_menu }}" form="bulkDeleteForm">
                            </td>
                            <td style="width: 80px;">
                                @if(!empty($m->gambar))
                                    <img src="{{ get_menu_image_url($m->gambar) }}" width="50" height="50" style="object-fit: cover;" class="rounded" alt="{{ $m->nama }}">
                                @endif
                            </td>

                            <td>
                                {{ $m->nama }}
                                @if($m->is_paket)
                                    <span class="badge bg-danger rounded-pill ms-2" style="font-size: 0.7rem;">Paket</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($m->kategori) }}</td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 150px;">
                                    {{ $m->deskripsi ?: '-' }}
                                </div>
                            </td>
                            <td>Rp {{ number_format($m->harga_beli, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                            <td>
                                @if($m->stok <= 0)
                                    <span class="badge bg-danger">Habis</span>
                                @else
                                    <span class="badge bg-success">{{ $m->stok }}</span>
                                @endif
                            </td>
                            <td>{{ $m->diskon ? $m->diskon . '%' : '-' }}</td>

                            <td class="text-end text-nowrap">
                                @if($m->is_paket)
                                    <a href="{{ route('admin.menu.paket.edit', $m->id_menu) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @else
                                    <a href="{{ route('admin.menu.edit', $m->id_menu) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endif

                                <form action="{{ route('admin.menu.destroy', $m->id_menu) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Belum ada menu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.menu-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const selectedCount = document.getElementById('selectedCount');

        function updateState() {
            const checkedBoxes = document.querySelectorAll('.menu-checkbox:checked');
            selectedCount.textContent = checkedBoxes.length;
            if (checkedBoxes.length > 0) {
                btnBulkDelete.removeAttribute('disabled');
            } else {
                btnBulkDelete.setAttribute('disabled', 'disabled');
            }
            if (checkAll) {
                checkAll.checked = (checkboxes.length > 0 && checkedBoxes.length === checkboxes.length);
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateState);
        });
    });
</script>
@endsection