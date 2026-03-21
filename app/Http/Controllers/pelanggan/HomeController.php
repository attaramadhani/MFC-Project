<?php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'pelanggan') {
            return redirect()->route('auth.login');
        }

        $menus = Menu::orderBy('kategori')->orderBy('nama')->get();

        $kategoriMap = [
            'geprek'   => 'Makanan',
            'crispy'   => 'Makanan',
            'gangnam'  => 'Makanan',
            'minuman'  => 'Minuman',
            'tambahan' => 'Tambahan',
        ];

        $menusByKategori = [];

        foreach ($menus as $m) {
            $raw = strtolower(trim($m->kategori ?? 'lainnya'));
            $key = $kategoriMap[$raw] ?? 'Lainnya';
            $menusByKategori[$key][] = $m;
        }

        $urutanKategori = ['Makanan', 'Minuman', 'Tambahan'];
        $orderedMenus = [];

        foreach ($urutanKategori as $kat) {
            if (isset($menusByKategori[$kat])) {
                $orderedMenus[$kat] = $menusByKategori[$kat];
            }
        }

        $cartRows = DB::table('keranjang')
            ->where('id_user', Auth::id())
            ->get(['id_menu', 'jumlah']);

        $cartByMenu = [];
        foreach ($cartRows as $row) {
            $cartByMenu[$row->id_menu] = (int) $row->jumlah;
        }

        return view('pelanggan.index', [
            'menusByKategori' => $orderedMenus,
            'cartByMenu' => $cartByMenu,
        ]);
    }
}