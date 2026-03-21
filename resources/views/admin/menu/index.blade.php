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

    <a href="{{ route('admin.menu.create') }}" class="btn btn-main text-white">
        + Tambah Menu
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success py-2">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $m)
                        <tr>
                            <td style="width: 80px;">
                                @if(!empty($m->gambar))
                                    <img src="{{ asset('img/' . $m->gambar) }}" width="50" class="rounded" alt="{{ $m->nama }}">
                                @endif
                            </td>

                            <td>{{ $m->nama }}</td>
                            <td>{{ ucfirst($m->kategori) }}</td>
                            <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>

                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.menu.edit', $m->id_menu) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

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
                            <td colspan="5" class="text-center text-muted py-4">
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