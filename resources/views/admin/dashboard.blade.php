@extends('layouts.admin')

@php
    $pageTitle = 'Dashboard';
@endphp

@section('content')
<div class="mb-3">
    <h2 class="h5 mb-1">Dashboard</h2>
    <div class="text-muted small">
        Ringkasan penjualan dan status pesanan toko GeprekinAja.
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Transaksi Berhasil</div>
            <div class="stat-value">{{ $totalTransaksiPaid }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Pelanggan Terdaftar</div>
            <div class="stat-value">{{ $totalPelanggan }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label mb-1">Total Menu</div>
            <div class="stat-value mb-2">{{ $totalMenu }}</div>
            <div class="small text-muted">
                Pesanan:
                <span class="badge-soft-gray status-pill">Created: {{ $statusCount['created'] }}</span>
                <span class="badge-soft-blue status-pill">Processing: {{ $statusCount['processing'] }}</span>
                <span class="badge-soft-amber status-pill">Ready: {{ $statusCount['ready'] }}</span>
                <span class="badge-soft-green status-pill">Csompleted: {{ $statusCount['completed'] }}</span>
                <span class="badge-soft-red status-pill">Canceled: {{ $statusCount['canceled'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="stat-label">Grafik Harian (30 Hari)</div>
                <div class="small text-muted">Pendapatan berdasarkan tanggal bayar</div>
            </div>
            <canvas id="chartDaily" height="80"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="stat-label">Grafik Bulanan (6 Bulan)</div>
                <div class="small text-muted">Pendapatan per bulan</div>
            </div>
            <canvas id="chartMonthly" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dailyLabels = @json($dailyLabels);
    const dailyValues = @json($dailyValues);
    const monthLabels = @json($monthLabels);
    const monthValues = @json($monthValues);

    const ctxDaily = document.getElementById('chartDaily').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Pendapatan',
                data: dailyValues,
                tension: 0.3,
                fill: false
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    const ctxMonthly = document.getElementById('chartMonthly').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Pendapatan',
                data: monthValues
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush