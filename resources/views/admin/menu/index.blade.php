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
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
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
                            <td style="width: 80px;">
                                @if(!empty($m->gambar))
                                    <img src="{{ asset('img/' . $m->gambar) }}" width="50" height="50" style="object-fit: cover;" class="rounded" alt="{{ $m->nama }}">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                Belum ada menu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection