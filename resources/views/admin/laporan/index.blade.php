@extends('layouts.admin')

@php
    $pageTitle = 'Laporan Penjualan';
@endphp

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="h5 mb-1">Laporan Penjualan</h2>
        <div class="text-muted small">
            Rekap transaksi berhasil berdasarkan tanggal pembayaran.
        </div>
    </div>

    <a class="btn btn-sm btn-outline-secondary"
       href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}"
       target="_blank">
        Export PDF
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>

            <div class="col-md-2">
                <button class="btn btn-sm btn-main text-white w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="small text-muted">Makanan Terjual</div>
            <div class="fw-bold">{{ $totalMakanan }} item</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="small text-muted">Minuman Terjual</div>
            <div class="fw-bold">{{ $totalMinuman }} item</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="small text-muted">Tambahan Terjual</div>
            <div class="fw-bold">{{ $totalTambahan }} item</div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="small text-muted">
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-end">Transaksi</th>
                        <th class="text-end">Makanan</th>
                        <th class="text-end">Minuman</th>
                        <th class="text-end">Tambahan</th>
                        <th class="text-end">Total Item</th>
                        <th class="text-end">Pendapatan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->tgl)->format('d M Y') }}</td>
                            <td class="text-end">{{ (int) $r->total_transaksi }}</td>
                            <td class="text-end">{{ (int) $r->makanan_qty }}</td>
                            <td class="text-end">{{ (int) $r->minuman_qty }}</td>
                            <td class="text-end">{{ (int) $r->tambahan_qty }}</td>
                            <td class="text-end">{{ (int) $r->total_item }}</td>
                            <td class="text-end">Rp {{ number_format((int) $r->total_pendapatan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($rows))
                <tfoot class="small text-muted">
                    <tr>
                        <th>Total</th>
                        <th class="text-end">{{ $totalTransaksi }}</th>
                        <th class="text-end">{{ $totalMakanan }}</th>
                        <th class="text-end">{{ $totalMinuman }}</th>
                        <th class="text-end">{{ $totalTambahan }}</th>
                        <th class="text-end">{{ $totalItem }}</th>
                        <th class="text-end">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection