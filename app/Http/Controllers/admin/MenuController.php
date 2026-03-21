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

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman,tambahan,geprek,crispy,gangnam',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $gambar = null;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $newName = 'menu_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $newName);
            $gambar = $newName;
        }

        DB::table('menu')->insert([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'harga' => (int) $request->harga,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu ditambahkan.');
    }

    public function edit($id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu, 404);

        return view('admin.menu.edit', compact('menu'));
    }

    public function update(Request $request, $id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu, 404);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:makanan,minuman,tambahan,geprek,crispy,gangnam',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
                'kategori' => $request->kategori,
                'harga' => (int) $request->harga,
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambar,
            ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu diupdate.');
    }

    public function destroy($id)
    {
        $menu = DB::table('menu')->where('id_menu', $id)->first();
        abort_if(!$menu, 404);

        DB::table('menu')->where('id_menu', $id)->delete();

        if ($menu->gambar && is_file(public_path('img/' . $menu->gambar))) {
            @unlink(public_path('img/' . $menu->gambar));
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu dihapus.');
    }
}