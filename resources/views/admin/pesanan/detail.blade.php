@php
    function humanPaymentStatusAdmin($s) {
        return match ($s) {
            'paid' => 'Sudah dibayar',
            'pending' => 'Menunggu pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kadaluarsa',
            default => 'Belum dibayar',
        };
    }

    function humanOrderStatusAdmin($s) {
        return match ($s) {
            'waiting_confirmation' => 'Menunggu konfirmasi',
            'processing' => 'Sedang diproses',
            'ready' => 'Diantar',
            'completed' => 'Selesai',
            'canceled' => 'Dibatalkan',
            default => 'Dibuat',
        };
    }
@endphp

<div class="order-detail-wrapper">

    <div class="pb-3 mb-3 border-bottom d-flex justify-content-between align-items-start">
        <div>
            <div class="small text-muted">Kode Pesanan</div>
            <div class="fw-semibold fs-6">{{ $order->kode_pesanan }}</div>
            <div class="small text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
            <div class="small text-muted">Pelanggan: {{ $order->nama_user }}</div>
        </div>

        <div class="text-end small">
            <div>{{ humanPaymentStatusAdmin($order->payment_status) }}</div>
            <div>{{ humanOrderStatusAdmin($order->order_status) }}</div>

            @if (!empty($order->paid_at))
                <div class="text-muted mt-1">
                    Dibayar: {{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') }}
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-3">
        <div class="card-body">
            <h6 class="fw-bold mb-2">Info Pengantaran</h6>

            <div class="row g-2">
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Metode Pembayaran</div>
                    <div>
                        @if(($order->payment_method ?? '') === 'cash')
                            <span class="badge bg-warning text-dark">COD (Bayar di tempat)</span>
                        @else
                            <span class="badge bg-secondary">Online</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted mb-1">Ongkir</div>
                    <div>Rp {{ number_format((int)($order->ongkir ?? 0), 0, ',', '.') }}</div>
                </div>
            </div>

            <hr>

            <div class="small text-muted mb-1">Wilayah</div>
            <div class="mb-2">{{ $order->wilayah_pengiriman ?? '-' }}</div>

            <div class="small text-muted mb-1">Alamat Lengkap</div>
            <div>{!! nl2br(e($order->alamat_pengiriman ?? '-')) !!}</div>
        </div>
    </div>

    <div class="mb-3 pb-2 border-bottom">
        <div class="small text-muted mb-2 fw-semibold">Item pesanan</div>

        @forelse($items as $row)
            @php
                $jumlah = (int) $row->jumlah;
                $harga = (int) $row->harga;
                $sub = $jumlah * $harga;
            @endphp

            <div class="d-flex justify-content-between py-2">
                <div>
                    <div class="fw-semibold">{{ $row->nama }}</div>
                    <div class="small text-muted">
                        x {{ $jumlah }} • Rp {{ number_format($harga,0,',','.') }}
                        @if (!empty($row->catatan_item))
                            <br><span class="fst-italic">Catatan: {{ $row->catatan_item }}</span>
                        @endif
                    </div>
                </div>

                <div class="fw-semibold small">
                    Rp {{ number_format($sub,0,',','.') }}
                </div>
            </div>
        @empty
            <div class="small text-muted">Tidak ada item untuk pesanan ini.</div>
        @endforelse
    </div>

    <div class="d-flex gap-4 flex-column flex-md-row">
        <div class="flex-fill">
            <div class="small text-muted mb-1 fw-semibold">Total</div>
            <div class="fw-semibold fs-6 mb-2">
                Rp {{ number_format((int)$order->total_harga,0,',','.') }}
            </div>

            @if ($pay)
                <div class="small text-muted">
                    Provider: {{ $pay->provider }}<br>
                    Metode: {{ $pay->metode }}<br>
                    Status: {{ humanPaymentStatusAdmin($pay->status) }}
                </div>
            @else
                <div class="small text-muted">Belum ada data pembayaran.</div>
            @endif
        </div>

        <div class="flex-fill">
            <div class="small text-muted fw-semibold mb-1">Riwayat Status</div>

            @forelse($logs as $log)
                <div class="small mb-1">
                    @if (!empty($log->dibuat_pada))
                        <span class="text-muted">{{ \Carbon\Carbon::parse($log->dibuat_pada)->format('d M Y H:i') }} · </span>
                    @endif

                    {{ $log->tipe }}:
                    <span class="fw-semibold">
                        {{ $log->status_lama ?? '-' }} → {{ $log->status_baru ?? '-' }}
                    </span>

                    @if (!empty($log->nama_user))
                        <span class="text-muted">· oleh {{ $log->nama_user }}</span>
                    @endif
                </div>
            @empty
                <div class="small text-muted">Belum ada riwayat.</div>
            @endforelse
        </div>
    </div>
</div>  