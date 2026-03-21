<?php
namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;

class OrderController extends Controller
{
    public function index()
    {
        $id_user = Auth::id();

        $orders = DB::table('pesanan as p')
            ->leftJoin('detail_pesanan as d', 'd.id_pesanan', '=', 'p.id_pesanan')
            ->where('p.id_user', $id_user)
            ->select(
                'p.id_pesanan',
                'p.kode_pesanan',
                'p.total_harga',
                'p.payment_status',
                'p.order_status',
                'p.created_at',
                DB::raw('COALESCE(SUM(d.jumlah), 0) as total_item')
            )
            ->groupBy(
                'p.id_pesanan',
                'p.kode_pesanan',
                'p.total_harga',
                'p.payment_status',
                'p.order_status',
                'p.created_at'
            )
            ->orderByDesc('p.created_at')
            ->get();

        return view('pelanggan.pesanan', compact('orders'));
    }

    public function show($id)
    {
        $id_user = Auth::id();

        $order = DB::table('pesanan')
            ->where('id_pesanan', $id)
            ->where('id_user', $id_user)
            ->first();

        if (!$order) {
            abort(404);
        }

        $items = DB::table('detail_pesanan as d')
            ->join('menu as m', 'm.id_menu', '=', 'd.id_menu')
            ->where('d.id_pesanan', $id)
            ->get([
                'd.jumlah',
                'd.harga',
                'd.catatan_item',
                'm.nama',
        ]);

        $pembayaran = DB::table('pembayaran')
            ->where('id_pesanan', $id)
            ->orderByDesc('id_pembayaran')
            ->first();

        return view('pelanggan.core.pesanan_detail', [
            'order' => $order,
            'items' => $items,
            'pembayaran' => $pembayaran,
        ]);
    }

    public function receipt($id)
    {
        $id_user = Auth::id();

        $order = DB::table('pesanan')
            ->where('id_pesanan', $id)
            ->where('id_user', $id_user)
            ->first();

        if (!$order || $order->order_status !== 'completed') {
            abort(404);
        }

        $items = DB::table('detail_pesanan as d')
            ->join('menu as m', 'm.id_menu', '=', 'd.id_menu')
            ->where('d.id_pesanan', $id)
            ->get([
                'd.jumlah',
                'd.harga',
                'd.catatan_item',
                'm.nama',
            ]);

        $pembayaran = DB::table('pembayaran')
            ->where('id_pesanan', $id)
            ->orderByDesc('id_pembayaran')
            ->first();

        $html = view('layouts.struk', [
            'order' => $order,
            'items' => $items,
            'pembayaran' => $pembayaran,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A7', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="struk-' . $order->kode_pesanan . '.pdf"');
    }
}