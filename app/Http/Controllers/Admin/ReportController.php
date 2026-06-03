<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    public function index()
    {
        $defaultFrom = now()->subDays(29)->toDateString();
        $defaultTo = now()->toDateString();

        $from = request('from', $defaultFrom);
        $to = request('to', $defaultTo);
        $filterType = request('filter_type', 'daily'); // daily, weekly, monthly, yearly

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        // Tentukan format grouping tanggal untuk PostgreSQL
        // daily: YYYY-MM-DD
        // weekly: YYYY-"W"IW (Year and ISO Week)
        // monthly: YYYY-MM
        // yearly: YYYY
        $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
        $groupBy = "CAST(p.paid_at AS DATE)";
        $orderBy = "tgl ASC";

        if ($filterType === 'weekly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY-\"W\"IW') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY-\"W\"IW')";
            $orderBy = "tgl ASC";
        } elseif ($filterType === 'monthly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY-MM') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY-MM')";
            $orderBy = "tgl ASC";
        } elseif ($filterType === 'yearly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY')";
            $orderBy = "tgl ASC";
        }

        $rows = DB::select("
            SELECT 
                {$dateSelect},
                COUNT(p.id_pesanan) AS total_transaksi,
                SUM(p.total_harga) AS total_pendapatan,
                SUM(oi.total_item) AS total_item,
                SUM(oi.makanan_qty) AS makanan_qty,
                SUM(oi.minuman_qty) AS minuman_qty,
                SUM(oi.tambahan_qty) AS tambahan_qty,
                SUM(oi.makanan_profit) AS makanan_profit,
                SUM(oi.minuman_profit) AS minuman_profit,
                SUM(oi.tambahan_profit) AS tambahan_profit
            FROM pesanan p
            JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah ELSE 0 END) AS makanan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah ELSE 0 END) AS minuman_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah ELSE 0 END) AS tambahan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS makanan_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS minuman_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS tambahan_profit
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND CAST(p.paid_at AS DATE) BETWEEN ? AND ?
            GROUP BY {$groupBy}
            ORDER BY {$orderBy}
        ", [$from, $to]);

        $totalPendapatan = 0;
        $totalTransaksi = 0;
        $totalItem = 0;
        $totalMakanan = 0;
        $totalMinuman = 0;
        $totalTambahan = 0;
        $totalMakananProfit = 0;
        $totalMinumanProfit = 0;
        $totalTambahanProfit = 0;

        foreach ($rows as $r) {
            $totalPendapatan += (int) $r->total_pendapatan;
            $totalTransaksi += (int) $r->total_transaksi;
            $totalItem += (int) $r->total_item;
            $totalMakanan += (int) $r->makanan_qty;
            $totalMinuman += (int) $r->minuman_qty;
            $totalTambahan += (int) $r->tambahan_qty;
            $totalMakananProfit += (int) $r->makanan_profit;
            $totalMinumanProfit += (int) $r->minuman_profit;
            $totalTambahanProfit += (int) $r->tambahan_profit;
        }

        return view('admin.laporan.index', compact(
            'rows',
            'from',
            'to',
            'filterType',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'totalMakanan',
            'totalMinuman',
            'totalTambahan',
            'totalMakananProfit',
            'totalMinumanProfit',
            'totalTambahanProfit'
        ));
    }

    public function export()
    {
        $defaultFrom = now()->subDays(29)->toDateString();
        $defaultTo = now()->toDateString();

        $from = request('from', $defaultFrom);
        $to = request('to', $defaultTo);
        $filterType = request('filter_type', 'daily');

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $dateSelect = "CAST(p.paid_at AS DATE) AS tgl";
        $groupBy = "CAST(p.paid_at AS DATE)";
        $orderBy = "tgl ASC";

        if ($filterType === 'weekly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY-\"W\"IW') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY-\"W\"IW')";
            $orderBy = "tgl ASC";
        } elseif ($filterType === 'monthly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY-MM') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY-MM')";
            $orderBy = "tgl ASC";
        } elseif ($filterType === 'yearly') {
            $dateSelect = "TO_CHAR(p.paid_at, 'YYYY') AS tgl";
            $groupBy = "TO_CHAR(p.paid_at, 'YYYY')";
            $orderBy = "tgl ASC";
        }

        $rows = DB::select("
            SELECT 
                {$dateSelect},
                COUNT(p.id_pesanan) AS total_transaksi,
                SUM(p.total_harga) AS total_pendapatan,
                SUM(oi.total_item) AS total_item,
                SUM(oi.makanan_qty) AS makanan_qty,
                SUM(oi.minuman_qty) AS minuman_qty,
                SUM(oi.tambahan_qty) AS tambahan_qty,
                SUM(oi.makanan_profit) AS makanan_profit,
                SUM(oi.minuman_profit) AS minuman_profit,
                SUM(oi.tambahan_profit) AS tambahan_profit
            FROM pesanan p
            JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah ELSE 0 END) AS makanan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah ELSE 0 END) AS minuman_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah ELSE 0 END) AS tambahan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam','makanan') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS makanan_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS minuman_profit,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) NOT IN ('geprek','crispy','gangnam','makanan','minuman') THEN dp.jumlah * (dp.harga - dp.diskon - dp.harga_beli) ELSE 0 END) AS tambahan_profit
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND CAST(p.paid_at AS DATE) BETWEEN ? AND ?
            GROUP BY {$groupBy}
            ORDER BY {$orderBy}
        ", [$from, $to]);

        $totalPendapatan = 0;
        $totalTransaksi = 0;
        $totalItem = 0;
        $totalMakanan = 0;
        $totalMinuman = 0;
        $totalTambahan = 0;
        $totalMakananProfit = 0;
        $totalMinumanProfit = 0;
        $totalTambahanProfit = 0;

        foreach ($rows as $r) {
            $totalPendapatan += (int) $r->total_pendapatan;
            $totalTransaksi += (int) $r->total_transaksi;
            $totalItem += (int) $r->total_item;
            $totalMakanan += (int) $r->makanan_qty;
            $totalMinuman += (int) $r->minuman_qty;
            $totalTambahan += (int) $r->tambahan_qty;
            $totalMakananProfit += (int) $r->makanan_profit;
            $totalMinumanProfit += (int) $r->minuman_profit;
            $totalTambahanProfit += (int) $r->tambahan_profit;
        }

        $admin = Auth::user()->nama_user ?? 'Administrator';

        $html = view('admin.laporan.export', compact(
            'rows',
            'from',
            'to',
            'filterType',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'totalMakanan',
            'totalMinuman',
            'totalTambahan',
            'totalMakananProfit',
            'totalMinumanProfit',
            'totalTambahanProfit',
            'admin'
        ))->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "laporan-{$from}-{$to}.pdf";

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}