<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class HomeController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('kategori')->orderBy('nama')->get();

        $kategoriMap = [
            'geprek' => 'Makanan',
            'crispy' => 'Makanan',
            'gangnam' => 'Makanan',
            'minuman' => 'Minuman',
            'tambahan' => 'Tambahan',
        ];

        $menusByKategori = [];

        foreach ($menus as $menu) {
            $raw = strtolower(trim($menu->kategori ?? 'lainnya'));
            $key = $kategoriMap[$raw] ?? 'Lainnya';
            $menusByKategori[$key][] = $menu;
        }

        return view('index', compact('menusByKategori'));
    }
}