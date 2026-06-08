<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = DB::table('menu')->orderByDesc('id_menu')->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function createPaket()
    {
        $nonPaketMenus = DB::table('menu')->where('is_paket', DB::raw('FALSE'))->get();
        return view('admin.menu.create_paket', compact('nonPaketMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'is_paket' => 'required|boolean',
            'kategori' => 'required|in:makanan,minuman,paket,tambahan',
            'harga' => 'required|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'stok' => 'required|integer|min:0',
            'diskon' => 'nullable|integer|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'komposisi_id_menu' => 'nullable|array',
            'komposisi_jumlah' => 'nullable|array',
        ]);

        $gambar = null;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $newName = 'menu_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            try {
                $file->move(public_path('img'), $newName);
                $gambar = $newName;
            } catch (\Exception $e) {
                // Ignore error on Vercel
                $gambar = null;
            }
        }

        $isPaket = $request->is_paket ? DB::raw('TRUE') : DB::raw('FALSE');
        $harga_beli = (int) ($request->harga_beli ?? 0);
        $harga_jual = (int) $request->harga;

        if ($request->is_paket && !empty($request->komposisi_id_menu)) {
            $harga_beli = 0;
            $harga_jual = 0;
            $komponenMenus = DB::table('menu')->whereIn('id_menu', $request->komposisi_id_menu)->get()->keyBy('id_menu');
            foreach ($request->komposisi_id_menu as $id_komponen) {
                $qty = $request->komposisi_jumlah[$id_komponen] ?? 1;
                if (isset($komponenMenus[$id_komponen])) {
                    $harga_beli += $komponenMenus[$id_komponen]->harga_beli * $qty;
                    $harga_jual += $komponenMenus[$id_komponen]->harga * $qty;
                }
            }
        }

        $id_menu = DB::table('menu')->insertGetId([
            'nama' => $request->nama,
            'is_paket' => $isPaket,
            'kategori' => $request->kategori,
            'harga' => $harga_jual,
            'harga_beli' => $harga_beli,
            'stok' => (int) $request->stok,
            'diskon' => (int) ($request->diskon ?? 0),
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar,
        ], 'id_menu');

        if ($request->is_paket && !empty($request->komposisi_id_menu)) {
            foreach ($request->komposisi_id_menu as $id_komponen) {
                if ($id_komponen) {
                    DB::table('paket_komposisi')->insert([
                        'id_menu_paket' => $id_menu,
                        'id_menu_komponen' => $id_komponen,
                        'jumlah' => $request->komposisi_jumlah[$id_komponen] ?? 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu ditambahkan.');
    }

    public function edit($id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu, 404);

        return view('admin.menu.edit', compact('menu'));
    }

    public function editPaket($id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu || !$menu->is_paket, 404);

        $nonPaketMenus = DB::table('menu')->where('is_paket', DB::raw('FALSE'))->get();
        $komposisi = DB::table('paket_komposisi')->where('id_menu_paket', $id)->get();

        return view('admin.menu.edit_paket', compact('menu', 'nonPaketMenus', 'komposisi'));
    }

    public function update(Request $request, $id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu, 404);

        $request->validate([
            'nama' => 'required|string|max:255',
            'is_paket' => 'required|boolean',
            'kategori' => 'required|in:makanan,minuman,paket,tambahan',
            'harga' => 'required|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'stok' => 'required|integer|min:0',
            'diskon' => 'nullable|integer|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'komposisi_id_menu' => 'nullable|array',
            'komposisi_jumlah' => 'nullable|array',
        ]);

        $gambar = $menu->gambar;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $newName = 'menu_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            try {
                $file->move(public_path('img'), $newName);
                if ($gambar && is_file(public_path('img/' . $gambar))) {
                    @unlink(public_path('img/' . $gambar));
                }
                $gambar = $newName;
            } catch (\Exception $e) {
                // Ignore error on Vercel
            }
        }

        $isPaket = $request->is_paket ? DB::raw('TRUE') : DB::raw('FALSE');
        $harga_beli = (int) ($request->harga_beli ?? 0);
        $harga_jual = (int) $request->harga;

        if ($request->is_paket && !empty($request->komposisi_id_menu)) {
            $harga_beli = 0;
            $harga_jual = 0;
            $komponenMenus = DB::table('menu')->whereIn('id_menu', $request->komposisi_id_menu)->get()->keyBy('id_menu');
            foreach ($request->komposisi_id_menu as $id_komponen) {
                $qty = $request->komposisi_jumlah[$id_komponen] ?? 1;
                if (isset($komponenMenus[$id_komponen])) {
                    $harga_beli += $komponenMenus[$id_komponen]->harga_beli * $qty;
                    $harga_jual += $komponenMenus[$id_komponen]->harga * $qty;
                }
            }
        }

        DB::table('menu')
            ->where('id_menu', $id)
            ->update([
                'nama' => $request->nama,
                'is_paket' => $isPaket,
                'kategori' => $request->kategori,
                'harga' => $harga_jual,
                'harga_beli' => $harga_beli,
                'stok' => (int) $request->stok,
                'diskon' => (int) ($request->diskon ?? 0),
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambar,
            ]);

        // Sync komposisi paket
        DB::table('paket_komposisi')->where('id_menu_paket', $id)->delete();
        if ($request->is_paket && !empty($request->komposisi_id_menu)) {
            foreach ($request->komposisi_id_menu as $id_komponen) {
                if ($id_komponen) {
                    DB::table('paket_komposisi')->insert([
                        'id_menu_paket' => $id,
                        'id_menu_komponen' => $id_komponen,
                        'jumlah' => $request->komposisi_jumlah[$id_komponen] ?? 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu diupdate.');
    }

    public function destroy($id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu, 404);

        // Hapus data keranjang yang berisi menu ini
        DB::table('keranjang')->where('id_menu', $id)->delete();

        // Hapus riwayat pesanan (detail) yang terkait dengan menu ini
        DB::table('detail_pesanan')->where('id_menu', $id)->delete();

        // Hapus komposisi paket jika ini paket
        DB::table('paket_komposisi')->where('id_menu_paket', $id)->delete();
        // Hapus komposisi jika ini komponen yang dipakai di paket
        DB::table('paket_komposisi')->where('id_menu_komponen', $id)->delete();

        DB::table('menu')->where('id_menu', $id)->delete();

        if ($menu->gambar && is_file(public_path('img/' . $menu->gambar))) {
            @unlink(public_path('img/' . $menu->gambar));
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu dihapus.');
    }
}