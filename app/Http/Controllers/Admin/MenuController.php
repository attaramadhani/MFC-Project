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
        $nonPaketMenus = DB::table('menu')->where('is_paket', 0)->get();
        return view('admin.menu.create', compact('nonPaketMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'is_paket' => 'required|boolean',
            'kategori' => 'required|in:makanan,minuman,paket,tambahan',
            'harga' => 'required|integer|min:0',
            'harga_beli' => 'required|integer|min:0',
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
            $file->move(public_path('img'), $newName);
            $gambar = $newName;
        }

        $id_menu = DB::table('menu')->insertGetId([
            'nama' => $request->nama,
            'is_paket' => (int) $request->is_paket,
            'kategori' => $request->kategori,
            'harga' => (int) $request->harga,
            'harga_beli' => (int) $request->harga_beli,
            'stok' => (int) $request->stok,
            'diskon' => (int) ($request->diskon ?? 0),
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar,
        ], 'id_menu');

        // Handle komposisi paket
        if ((int) $request->is_paket === 1 && !empty($request->komposisi_id_menu)) {
            foreach ($request->komposisi_id_menu as $index => $id_komponen) {
                if ($id_komponen) {
                    DB::table('paket_komposisi')->insert([
                        'id_menu_paket' => $id_menu,
                        'id_menu_komponen' => $id_komponen,
                        'jumlah' => $request->komposisi_jumlah[$index] ?? 1,
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

        $nonPaketMenus = DB::table('menu')->where('is_paket', 0)->get();
        $komposisi = [];
        if ($menu->is_paket) {
            $komposisi = DB::table('paket_komposisi')->where('id_menu_paket', $id)->get();
        }

        return view('admin.menu.edit', compact('menu', 'nonPaketMenus', 'komposisi'));
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
            'harga_beli' => 'required|integer|min:0',
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
            $file->move(public_path('img'), $newName);

            if ($gambar && is_file(public_path('img/' . $gambar))) {
                @unlink(public_path('img/' . $gambar));
            }

            $gambar = $newName;
        }

        DB::table('menu')
            ->where('id_menu', $id)
            ->update([
                'nama' => $request->nama,
                'is_paket' => (int) $request->is_paket,
                'kategori' => $request->kategori,
                'harga' => (int) $request->harga,
                'harga_beli' => (int) $request->harga_beli,
                'stok' => (int) $request->stok,
                'diskon' => (int) ($request->diskon ?? 0),
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambar,
            ]);

        // Sync komposisi paket
        DB::table('paket_komposisi')->where('id_menu_paket', $id)->delete();
        if ((int) $request->is_paket === 1 && !empty($request->komposisi_id_menu)) {
            foreach ($request->komposisi_id_menu as $index => $id_komponen) {
                if ($id_komponen) {
                    DB::table('paket_komposisi')->insert([
                        'id_menu_paket' => $id,
                        'id_menu_komponen' => $id_komponen,
                        'jumlah' => $request->komposisi_jumlah[$index] ?? 1,
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