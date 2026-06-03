<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class HomeController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('kategori')->orderBy('nama')->get();

        $kategoriMap = [
            'makanan'  => 'Makanan',
            'minuman'  => 'Minuman',
            'paket'    => 'Paket',
            'tambahan' => 'Tambahan',
        ];

        $menusPaket = [];
        $menusByKategori = [];

        foreach ($menus as $menu) {
            if ($menu->is_paket) {
                $menusPaket[] = $menu;
            } else {
                $raw = strtolower(trim($menu->kategori ?? 'lainnya'));
                $key = $kategoriMap[$raw] ?? 'Lainnya';
                $menusByKategori[$key][] = $menu;
            }
        }

        return view('index', compact('menusPaket', 'menusByKategori'));
    }
}