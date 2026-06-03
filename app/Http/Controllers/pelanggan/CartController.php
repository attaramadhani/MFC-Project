<?php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $id_user = Auth::id();
        $id_menu = (int) $request->input('id_menu', 0);
        $jumlah  = (int) $request->input('jumlah', 1);

        if ($id_menu <= 0 || $jumlah <= 0) {
            return redirect()->route('pelanggan.index');
        }

        $menu = DB::table('menu')->where('id_menu', $id_menu)->first();
        if (!$menu) {
            return redirect()->route('pelanggan.index')->with('error', 'Menu tidak ditemukan.');
        }

        $existing = DB::table('keranjang')
            ->where('id_user', $id_user)
            ->where('id_menu', $id_menu)
            ->first();

        $requestedQty = $jumlah + ($existing ? $existing->jumlah : 0);

        if ($requestedQty > $menu->stok) {
            return redirect()->route('pelanggan.index')->with('error', 'Stok tidak mencukupi. Hanya tersisa ' . $menu->stok . ' porsi.');
        }

        if ($existing) {
            DB::table('keranjang')
                ->where('id_keranjang', $existing->id_keranjang)
                ->update([
                    'jumlah' => $requestedQty
                ]);
        } else {
            DB::table('keranjang')->insert([
                'id_user' => $id_user,
                'id_menu' => $id_menu,
                'jumlah'  => $jumlah,
            ]);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $id_user = Auth::id();
        $id_menu = (int) $request->input('id_menu', 0);
        $action  = $request->input('action', '');

        if ($id_menu <= 0 || !in_array($action, ['plus', 'minus', 'set'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
            ]);
        }

        $row = DB::table('keranjang')
            ->where('id_user', $id_user)
            ->where('id_menu', $id_menu)
            ->first();

        $menu = DB::table('menu')->where('id_menu', $id_menu)->first();
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan.',
            ]);
        }

        $newQty = 0;

        if ($action === 'set') {
            $newQty = max(0, (int) $request->input('value', 0));

            if ($newQty > 0) {
                if ($newQty > $menu->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi. Tersisa ' . $menu->stok . ' porsi.',
                    ]);
                }

                if ($row) {
                    DB::table('keranjang')
                        ->where('id_keranjang', $row->id_keranjang)
                        ->update(['jumlah' => $newQty]);
                } else {
                    DB::table('keranjang')->insert([
                        'id_user' => $id_user,
                        'id_menu' => $id_menu,
                        'jumlah'  => $newQty,
                    ]);
                }
            } else {
                if ($row) {
                    DB::table('keranjang')
                        ->where('id_keranjang', $row->id_keranjang)
                        ->delete();
                }

                $newQty = 0;
            }

            return $this->cartResponse($id_user, $id_menu, $newQty);
        }

        if (!$row) {
            if ($action === 'plus') {
                if ($menu->stok < 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok habis.',
                    ]);
                }
                DB::table('keranjang')->insert([
                    'id_user' => $id_user,
                    'id_menu' => $id_menu,
                    'jumlah'  => 1,
                ]);
                $newQty = 1;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Item belum ada di keranjang',
                ]);
            }
        } else {
            $newQty = (int) $row->jumlah;

            if ($action === 'plus') {
                $newQty++;
            } elseif ($action === 'minus') {
                $newQty--;
            }

            if ($newQty > 0) {
                if ($newQty > $menu->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi. Tersisa ' . $menu->stok . ' porsi.',
                    ]);
                }
                DB::table('keranjang')
                    ->where('id_keranjang', $row->id_keranjang)
                    ->update(['jumlah' => $newQty]);
            } else {
                DB::table('keranjang')
                    ->where('id_keranjang', $row->id_keranjang)
                    ->delete();

                $newQty = 0;
            }
        }

        return $this->cartResponse($id_user, $id_menu, $newQty);
    }

    public function content()
    {
        if (!Auth::check()) {
            return response('<div class="text-center text-muted py-4 small">Silakan login dulu.</div>');
        }

        $id_user = Auth::id();

        $items = DB::table('keranjang as k')
            ->join('menu as m', 'm.id_menu', '=', 'k.id_menu')
            ->where('k.id_user', $id_user)
            ->orderBy('m.nama')
            ->get([
                'k.id_menu',
                'k.jumlah',
                'm.nama',
                'm.harga',
                'm.diskon',
            ]);

        if ($items->isEmpty()) {
            return response()->view('pelanggan.core.cart_content', [
                'items' => collect(),
                'total_harga' => 0,
            ]);
        }

        $total_harga = 0;
        foreach ($items as $row) {
            $harga_jual = (int) $row->harga;
            $diskon_persen = (int) ($row->diskon ?? 0);
            $harga_final = $harga_jual - ($harga_jual * $diskon_persen / 100);
            $total_harga += ((int) $row->jumlah * $harga_final);
        }

        return view('pelanggan.core.cart_content', [
            'items' => $items,
            'total_harga' => $total_harga,
        ]);
    }

    private function cartResponse($id_user, $id_menu, $newQty)
    {
        $cart_count = (int) (DB::table('keranjang')
            ->where('id_user', $id_user)
            ->sum('jumlah') ?? 0);

        $cart_total = (int) (DB::table('keranjang as k')
            ->join('menu as m', 'm.id_menu', '=', 'k.id_menu')
            ->where('k.id_user', $id_user)
            ->selectRaw('SUM(k.jumlah * (m.harga - (m.harga * m.diskon / 100))) as total_harga')
            ->value('total_harga') ?? 0);

        $item_subtotal = 0;

        if ($newQty > 0) {
            $rowItem = DB::table('keranjang as k')
                ->join('menu as m', 'm.id_menu', '=', 'k.id_menu')
                ->where('k.id_user', $id_user)
                ->where('k.id_menu', $id_menu)
                ->select('k.jumlah', 'm.harga', 'm.diskon')
                ->first();

            if ($rowItem) {
                $harga_jual = (int) $rowItem->harga;
                $diskon_persen = (int) ($rowItem->diskon ?? 0);
                $harga_final = $harga_jual - ($harga_jual * $diskon_persen / 100);
                $item_subtotal = (int) $rowItem->jumlah * $harga_final;
            }
        }

        return response()->json([
            'success'       => true,
            'new_qty'       => $newQty,
            'item_subtotal' => $item_subtotal,
            'cart_total'    => $cart_total,
            'cart_count'    => $cart_count,
        ]);
    }
}