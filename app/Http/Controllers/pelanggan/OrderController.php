<?php
namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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

    public function checkStatus($id)
    {
        $id_user = Auth::id();
        $order = DB::table('pesanan')->where('id_pesanan', $id)->where('id_user', $id_user)->first();

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $pembayaran = DB::table('pembayaran')
            ->where('id_pesanan', $id)
            ->orderByDesc('id_pembayaran')
            ->first();

        $order_id = $pembayaran->provider_order_id ?? $order->kode_pesanan;
        
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $baseUrl = $isProduction 
            ? 'https://api.midtrans.com/v2' 
            : 'https://api.sandbox.midtrans.com/v2';

        $url = "$baseUrl/$order_id/status";

        try {
            Log::info("MFC CHECK: Mengecek status untuk order_id: $order_id");

            $response = Http::withoutVerifying()
                ->withBasicAuth($serverKey, '')
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['transaction_status'] ?? '';
                
                Log::info("MFC CHECK: Response Midtrans untuk $order_id adalah $status", $data);

                if ($status === 'settlement' || $status === 'capture') {
                    DB::beginTransaction();
                    DB::table('pesanan')->where('id_pesanan', $id)->update([
                        'payment_status' => 'paid',
                        'paid_at' => $data['settlement_time'] ?? now()
                    ]);

                    if ($pembayaran) {
                        DB::table('pembayaran')->where('id_pembayaran', $pembayaran->id_pembayaran)->update([
                            'status' => 'paid',
                            'settlement_time' => $data['settlement_time'] ?? now(),
                            'provider_transaction_id' => $data['transaction_id'] ?? null,
                            'raw_response' => json_encode($data)
                        ]);
                    } else {
                        // Jika record pembayaran belum ada, buat baru
                        DB::table('pembayaran')->insert([
                            'id_pesanan' => $id,
                            'provider' => 'midtrans',
                            'metode' => $data['payment_type'] ?? 'online',
                            'gross_amount' => $data['gross_amount'] ?? $order->total_harga,
                            'status' => 'paid',
                            'transaction_time' => $data['transaction_time'] ?? now(),
                            'settlement_time' => $data['settlement_time'] ?? now(),
                            'provider_order_id' => $order_id,
                            'provider_transaction_id' => $data['transaction_id'] ?? null,
                            'raw_response' => json_encode($data),
                            'created_at' => now(),
                        ]);
                    }
                    DB::commit();

                    return redirect()->route('pelanggan.orders.index')->with('success', 'Pembayaran berhasil dikonfirmasi!');
                } else {
                    $msg = ($status === 'pending') ? 'Menunggu pembayaran / Sedang diproses.' : 'Status: ' . $status;
                    return redirect()->route('pelanggan.orders.index')->with('info', $msg);
                }
            } else {
                Log::error("MFC CHECK: Gagal koneksi ke Midtrans", ['status' => $response->status(), 'body' => $response->body()]);
                return redirect()->route('pelanggan.orders.index')->with('error', 'Gagal verifikasi ke Midtrans (HTTP ' . $response->status() . '). Silakan cek status manual nanti.');
            }
        } catch (\Exception $e) {
            Log::error("MFC CHECK: Exception terjadi", ['msg' => $e->getMessage()]);
            return redirect()->route('pelanggan.orders.index')->with('error', 'Terjadi gangguan koneksi saat verifikasi.');
        }
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