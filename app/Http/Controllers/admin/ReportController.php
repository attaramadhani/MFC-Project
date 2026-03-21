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
        $defaultFrom = now()->subDays(6)->toDateString();
        $defaultTo = now()->toDateString();

        $from = request('from', $defaultFrom);
        $to = request('to', $defaultTo);

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $rows = DB::select("
            SELECT 
                DATE(p.paid_at) AS tgl,
                COUNT(*) AS total_transaksi,
                SUM(p.total_harga) AS total_pendapatan,
                SUM(oi.total_item) AS total_item,
                SUM(oi.makanan_qty) AS makanan_qty,
                SUM(oi.minuman_qty) AS minuman_qty,
                SUM(oi.tambahan_qty) AS tambahan_qty
            FROM pesanan p
            JOIN (
                SELECT 
                    dp.id_pesanan,
                    SUM(dp.jumlah) AS total_item,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam') THEN dp.jumlah ELSE 0 END) AS makanan_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah ELSE 0 END) AS minuman_qty,
                    SUM(CASE WHEN LOWER(TRIM(m.kategori)) = 'tambahan' THEN dp.jumlah ELSE 0 END) AS tambahan_qty
                FROM detail_pesanan dp
                JOIN menu m ON m.id_menu = dp.id_menu
                GROUP BY dp.id_pesanan
            ) oi ON oi.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND DATE(p.paid_at) BETWEEN ? AND ?
            GROUP BY DATE(p.paid_at)
            ORDER BY tgl ASC
        ", [$from, $to]);

        $totalPendapatan = 0;
        $totalTransaksi = 0;
        $totalItem = 0;
        $totalMakanan = 0;
        $totalMinuman = 0;
        $totalTambahan = 0;

        foreach ($rows as $r) {
            $totalPendapatan += (int) $r->total_pendapatan;
            $totalTransaksi += (int) $r->total_transaksi;
            $totalItem += (int) $r->total_item;
            $totalMakanan += (int) $r->makanan_qty;
            $totalMinuman += (int) $r->minuman_qty;
            $totalTambahan += (int) $r->tambahan_qty;
        }

        return view('admin.laporan.index', compact(
            'rows',
            'from',
            'to',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
            'totalMakanan',
            'totalMinuman',
            'totalTambahan'
        ));
    }

    public function export()
    {
        $defaultFrom = now()->subDays(6)->toDateString();
        $defaultTo = now()->toDateString();

        $from = request('from', $defaultFrom);
        $to = request('to', $defaultTo);

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $rows = DB::select("
            SELECT 
                DATE(p.paid_at) AS tgl,
                COUNT(*) AS total_transaksi,
                SUM(p.total_harga) AS total_pendapatan,
                SUM(dp_total.total_item) AS total_item
            FROM pesanan p
            JOIN (
                SELECT id_pesanan, SUM(jumlah) AS total_item
                FROM detail_pesanan
                GROUP BY id_pesanan
            ) dp_total ON dp_total.id_pesanan = p.id_pesanan
            WHERE p.payment_status = 'paid'
              AND DATE(p.paid_at) BETWEEN ? AND ?
            GROUP BY DATE(p.paid_at)
            ORDER BY tgl ASC
        ", [$from, $to]);

        $totalPendapatan = 0;
        $totalTransaksi = 0;
        $totalItem = 0;

        foreach ($rows as $r) {
            $totalPendapatan += (int) $r->total_pendapatan;
            $totalTransaksi += (int) $r->total_transaksi;
            $totalItem += (int) $r->total_item;
        }

        $admin = Auth::user()->nama_user ?? 'Administrator';

        $html = view('admin.laporan.export', compact(
            'rows',
            'from',
            'to',
            'totalPendapatan',
            'totalTransaksi',
            'totalItem',
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