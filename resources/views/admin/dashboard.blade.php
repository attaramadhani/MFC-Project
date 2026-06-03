@extends('layouts.admin')

@php
    $pageTitle = 'Dashboard';
    $now = \Carbon\Carbon::now('Asia/Jakarta');
@endphp

@section('content')

{{-- ══ PAGE HEADER ══ --}}
<div class="db-header">
    <div>
        <h1 class="db-title">Dashboard</h1>
        <p class="db-subtitle">Ringkasan penjualan MFC · {{ $now->translatedFormat('d F Y') }}</p>
    </div>
    <div id="db-clock" class="db-clock"></div>
</div>

{{-- ══ STAT CARDS (4 kolom) ══ --}}
<div class="db-stats">

    <div class="db-card db-card--red">
        <div class="db-card-label">Pendapatan Hari Ini</div>
        <div class="db-card-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
        <div class="db-card-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>

    <div class="db-card db-card--blue">
        <div class="db-card-label">Pendapatan Bulan Ini</div>
        <div class="db-card-value">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
        <div class="db-card-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
    </div>

    <div class="db-card db-card--green">
        <div class="db-card-label">Transaksi Berhasil</div>
        <div class="db-card-value">{{ $totalTransaksiPaid }}</div>
        <div class="db-card-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
    </div>

    <div class="db-card db-card--purple">
        <div class="db-card-label">Pelanggan Terdaftar</div>
        <div class="db-card-value">{{ $totalPelanggan }}</div>
        <div class="db-card-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>

</div>

{{-- ══ ORDER STATUS PANEL ══ --}}
<div class="db-panel">
    <div class="db-panel-head">
        <span class="db-panel-title">Status Pesanan</span>
        <span class="db-panel-meta">{{ array_sum($statusCount) }} total · {{ $totalMenu }} menu aktif</span>
    </div>

    @php
        $total = array_sum($statusCount) ?: 1;
        $statuses = [
            'Selesai'    => ['count' => $statusCount['completed'],  'color' => '#10B981'],
            'Diproses'   => ['count' => $statusCount['processing'], 'color' => '#3B82F6'],
            'Siap'       => ['count' => $statusCount['ready'],      'color' => '#F59E0B'],
            'Dibuat'     => ['count' => $statusCount['created'],    'color' => '#9CA3AF'],
            'Dibatalkan' => ['count' => $statusCount['canceled'],   'color' => '#EF4444'],
        ];
    @endphp

    <div class="db-status-bar">
        @foreach($statuses as $label => $s)
            @if($s['count'] > 0)
                <div class="db-status-seg" style="flex:{{ $s['count'] }}; background:{{ $s['color'] }};" title="{{ $label }}: {{ $s['count'] }}"></div>
            @endif
        @endforeach
    </div>

    <div class="db-status-legend">
        @foreach($statuses as $label => $s)
        <div class="db-legend-item">
            <span class="db-legend-dot" style="background:{{ $s['color'] }};"></span>
            <span class="db-legend-label">{{ $label }}</span>
            <span class="db-legend-count">{{ $s['count'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ══ CHARTS ROW ══ --}}
<div class="db-charts">

    <div class="db-panel">
        <div class="db-panel-head">
            <span class="db-panel-title">Pendapatan Harian</span>
            <span class="db-panel-meta">30 hari terakhir</span>
        </div>
        <canvas id="chartDaily"></canvas>
    </div>

    <div class="db-panel">
        <div class="db-panel-head">
            <span class="db-panel-title">Pendapatan Bulanan</span>
            <span class="db-panel-meta">6 bulan terakhir</span>
        </div>
        <canvas id="chartMonthly"></canvas>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Live Clock ── */
    function tick() {
        const el = document.getElementById('db-clock');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    tick(); setInterval(tick, 1000);

    const dailyLabels = @json($dailyLabels);
    const dailyValues = @json($dailyValues);
    const monthLabels = @json($monthLabels);
    const monthValues = @json($monthValues);

    const fmtRp = v => 'Rp ' + Number(v).toLocaleString('id-ID');

    const scaleY = {
        grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
        ticks: { color: '#9CA3AF', font: { family: 'Inter', size: 11 }, callback: fmtRp },
        border: { display: false }
    };
    const scaleX = {
        grid: { display: false },
        ticks: { color: '#9CA3AF', font: { family: 'Inter', size: 10 }, maxRotation: 0, maxTicksLimit: 8 },
        border: { display: false }
    };
    const tooltip = {
        backgroundColor: '#111827',
        titleColor: '#F9FAFB',
        bodyColor: '#D1D5DB',
        padding: 10,
        cornerRadius: 8,
        callbacks: { label: ctx => '  ' + fmtRp(ctx.parsed.y) }
    };

    /* ── Daily Line ── */
    const ctxD = document.getElementById('chartDaily').getContext('2d');
    const gradD = ctxD.createLinearGradient(0, 0, 0, 200);
    gradD.addColorStop(0, 'rgba(192,57,43,0.18)');
    gradD.addColorStop(1, 'rgba(192,57,43,0)');

    new Chart(ctxD, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                data: dailyValues,
                borderColor: '#C0392B',
                backgroundColor: gradD,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#C0392B',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip },
            scales: { y: scaleY, x: scaleX }
        }
    });

    /* ── Monthly Bar ── */
    const ctxM = document.getElementById('chartMonthly').getContext('2d');
    const gradM = ctxM.createLinearGradient(0, 0, 0, 200);
    gradM.addColorStop(0, 'rgba(59,130,246,0.8)');
    gradM.addColorStop(1, 'rgba(59,130,246,0.25)');

    new Chart(ctxM, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                data: monthValues,
                backgroundColor: gradM,
                hoverBackgroundColor: '#3B82F6',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip },
            scales: { y: scaleY, x: scaleX }
        }
    });
});
</script>
@endpush