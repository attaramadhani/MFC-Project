@extends('layouts.admin')

@php
    $title = 'Kelola Pesanan';
    $pageTitle = 'Kelola Pesanan';

    function badgePaymentAdmin($status) {
        return match ($status) {
            'paid' => '<span class="status-pill badge-soft-green">Sudah dibayar</span>',
            'pending' => '<span class="status-pill badge-soft-amber">Menunggu</span>',
            'failed' => '<span class="status-pill badge-soft-red">Gagal</span>',
            'expired' => '<span class="status-pill badge-soft-gray">Kadaluarsa</span>',
            default => '<span class="status-pill badge-soft-gray">Belum dibayar</span>',
        };
    }

    function badgeOrderAdmin($status) {
        return match ($status) {
            'waiting_confirmation' => '<span class="status-pill badge-soft-amber">Menunggu konfirmasi</span>',
            'processing' => '<span class="status-pill badge-soft-blue">Diproses</span>',
            'ready' => '<span class="status-pill badge-soft-amber">Diantar</span>',
            'completed' => '<span class="status-pill badge-soft-green">Selesai</span>',
            'canceled' => '<span class="status-pill badge-soft-red">Dibatalkan</span>',
            default => '<span class="status-pill badge-soft-gray">Dibuat</span>',
        };
    }
@endphp

@section('content')
<div class="mb-3">
    <h2 class="h5 mb-1">Kelola Pesanan</h2>
    <div class="text-muted small">
        Lihat semua pesanan, status pembayaran, dan ubah status proses di sini.
    </div>
</div>


<form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-end mb-3">
    <div class="col-md-3">
        <label class="form-label small mb-1">Status Pesanan</label>
        <select name="status" class="form-select form-select-sm">
            <option value="">Semua</option>
            <option value="created" {{ $statusFilter === 'created' ? 'selected' : '' }}>Dibuat</option>
            <option value="waiting_confirmation" {{ $statusFilter === 'waiting_confirmation' ? 'selected' : '' }}>Menunggu konfirmasi</option>
            <option value="processing" {{ $statusFilter === 'processing' ? 'selected' : '' }}>Diproses</option>
            <option value="ready" {{ $statusFilter === 'ready' ? 'selected' : '' }}>Diantar</option>
            <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="canceled" {{ $statusFilter === 'canceled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label small mb-1">Status Pembayaran</label>
        <select name="pay_status" class="form-select form-select-sm">
            <option value="">Semua</option>
            <option value="unpaid" {{ $payFilter === 'unpaid' ? 'selected' : '' }}>Belum dibayar</option>
            <option value="pending" {{ $payFilter === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ $payFilter === 'paid' ? 'selected' : '' }}>Sudah dibayar</option>
            <option value="failed" {{ $payFilter === 'failed' ? 'selected' : '' }}>Gagal</option>
            <option value="expired" {{ $payFilter === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label small mb-1">Dari Tanggal</label>
        <input type="date" name="from" class="form-control form-control-sm" value="{{ $dateFrom }}">
    </div>

    <div class="col-md-2">
        <label class="form-label small mb-1">Sampai</label>
        <input type="date" name="to" class="form-control form-control-sm" value="{{ $dateTo }}">
    </div>

    <div class="col-md-2">
        <button class="btn btn-main text-white w-100">Tampilkan</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Tgl & Jam</th>
                <th>Total</th>
                <th>Pembayaran</th>
                <th>Status Pesanan</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $o)
                <tr>
                    <td class="fw-semibold">{{ $o->kode_pesanan }}</td>
                    <td>{{ $o->nama_user }}</td>
                    <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d M Y, H:i') }}</td>
                    <td>Rp {{ number_format($o->total_harga, 0, ',', '.') }}</td>
                    <td>{!! badgePaymentAdmin($o->payment_status) !!}</td>
                    <td>{!! badgeOrderAdmin($o->order_status) !!}</td>
                    <td class="text-end">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary me-1 btn-order-detail"
                            data-id="{{ $o->id_pesanan }}">
                            Detail
                        </button>

                        <form method="POST"
                              action="{{ route('admin.orders.status', $o->id_pesanan) }}"
                              class="d-inline-block">
                            @csrf
                            <select name="order_status"
                                    class="form-select form-select-sm d-inline w-auto"
                                    onchange="this.form.submit()">
                                <option value="">Status…</option>

                                @php
                                    $current = $o->order_status;
                                    $allowed = [
                                        'created' => ['processing','canceled'],
                                        'waiting_confirmation' => ['processing','canceled'],
                                        'processing' => ['ready','canceled'],
                                        'ready' => ['completed','canceled'],
                                        'completed' => [],
                                        'canceled' => [],
                                    ];

                                    $labels = [
                                        'processing' => 'Diproses',
                                        'ready' => 'Diantar',
                                        'completed' => 'Selesai',
                                        'canceled' => 'Dibatalkan',
                                    ];
                                @endphp

                                @foreach(($allowed[$current] ?? []) as $opt)
                                    <option value="{{ $opt }}">{{ $labels[$opt] ?? $opt }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">Belum ada pesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailBody">
                <div class="text-center text-muted small py-4">
                    Memuat detail...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('orderDetailModal');
    const modalBody = document.getElementById('orderDetailBody');
    const bsModal = new bootstrap.Modal(modalEl);
    const detailBaseUrl = "{{ url('/admin/pesanan') }}";

    document.querySelectorAll('.btn-order-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            modalBody.innerHTML = '<div class="text-center text-muted small py-4">Memuat detail...</div>';

            fetch(`${detailBaseUrl}/${encodeURIComponent(id)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(res => res.text())
            .then(html => {
                modalBody.innerHTML = html;
                bsModal.show();
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = '<div class="text-danger small">Gagal memuat detail.</div>';
                bsModal.show();
            });
        });
    });
});
</script>
@endpush