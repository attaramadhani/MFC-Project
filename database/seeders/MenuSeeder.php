<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu')->insert([
            [
                'id_menu' => 1,
                'nama' => 'Geprek Hemat',
                'deskripsi' => 'Ayam geprek pedas dengan nasi, porsi hemat cocok untuk sehari-hari.',
                'harga' => 10000.00,
                'kategori' => 'geprek',
                'gambar' => 'ayamgeprek.jpg',
                'dibuat_pada' => '2025-11-02 01:19:54',
            ],
            [
                'id_menu' => 2,
                'nama' => 'Geprek Jumbo',
                'deskripsi' => 'Porsi besar ayam geprek dengan rasa mantap, bikin kenyang lebih lama.',
                'harga' => 14000.00,
                'kategori' => 'geprek',
                'gambar' => 'geprekjumbo.jpg',
                'dibuat_pada' => '2025-11-02 01:21:11',
            ],
            [
                'id_menu' => 3,
                'nama' => 'Crispy Hemat',
                'deskripsi' => 'Ayam crispy renyah dengan nasi, pas buat kantong pelajar.',
                'harga' => 9000.00,
                'kategori' => 'crispy',
                'gambar' => 'crispyhemat.webp',
                'dibuat_pada' => '2025-11-02 01:21:35',
            ],
            [
                'id_menu' => 4,
                'nama' => 'Crispy Jumbo',
                'deskripsi' => 'Ayam crispy ukuran jumbo, garing di luar lembut di dalam.',
                'harga' => 13000.00,
                'kategori' => 'crispy',
                'gambar' => 'crispyjumbo.webp',
                'dibuat_pada' => '2025-11-02 01:21:59',
            ],
            [
                'id_menu' => 5,
                'nama' => 'Gangnam Hemat',
                'deskripsi' => 'Ayam saus Korea rasa gurih manis pedas, paket hemat.',
                'harga' => 12000.00,
                'kategori' => 'gangnam',
                'gambar' => 'gangnamhemat.webp',
                'dibuat_pada' => '2025-11-02 01:22:31',
            ],
            [
                'id_menu' => 6,
                'nama' => 'Gangnam Jumbo',
                'deskripsi' => 'Ayam saus Korea porsi besar, cocok untuk yang doyan banget.',
                'harga' => 16000.00,
                'kategori' => 'gangnam',
                'gambar' => 'gangnamjumbo.webp',
                'dibuat_pada' => '2025-11-02 01:23:02',
            ],
            [
                'id_menu' => 7,
                'nama' => 'Gangnam Chicken Double',
                'deskripsi' => 'Dua potong ayam saus Korea lezat, puas buat sharing berdua.',
                'harga' => 20000.00,
                'kategori' => 'gangnam',
                'gambar' => 'gangnamdouble.webp',
                'dibuat_pada' => '2025-11-02 01:32:59',
            ],
            [
                'id_menu' => 8,
                'nama' => 'Paha Bawah',
                'deskripsi' => 'Potongan paha bawah ayam goreng gurih, nikmat disantap kapan saja.',
                'harga' => 9000.00,
                'kategori' => 'tambahan',
                'gambar' => 'pahabawah.webp',
                'dibuat_pada' => '2025-11-02 01:29:34',
            ],
            [
                'id_menu' => 9,
                'nama' => 'Sayap',
                'deskripsi' => 'Sayap ayam goreng renyah, cocok buat camilan atau tambahan lauk.',
                'harga' => 9000.00,
                'kategori' => 'tambahan',
                'gambar' => 'sayap.webp',
                'dibuat_pada' => '2025-11-02 01:29:59',
            ],
            [
                'id_menu' => 10,
                'nama' => 'Paha Atas',
                'deskripsi' => 'Paha atas ayam goreng empuk dengan cita rasa gurih.',
                'harga' => 12000.00,
                'kategori' => 'tambahan',
                'gambar' => 'pahaatas.webp',
                'dibuat_pada' => '2025-11-02 01:30:27',
            ],
            [
                'id_menu' => 11,
                'nama' => 'Dada',
                'deskripsi' => 'Daging dada ayam goreng lezat, potongan besar bikin puas.',
                'harga' => 12000.00,
                'kategori' => 'tambahan',
                'gambar' => 'dada.webp',
                'dibuat_pada' => '2025-11-02 01:30:48',
            ],
            [
                'id_menu' => 12,
                'nama' => 'Le Minerale 600ml',
                'deskripsi' => 'Air mineral segar untuk menemani makanmu.',
                'harga' => 4000.00,
                'kategori' => 'minuman',
                'gambar' => 'leminerale.webp',
                'dibuat_pada' => '2025-11-02 01:31:24',
            ],
            [
                'id_menu' => 13,
                'nama' => 'Teh Pucuk 350ml',
                'deskripsi' => 'Teh manis menyegarkan, pas banget buat makan siang.',
                'harga' => 4000.00,
                'kategori' => 'minuman',
                'gambar' => 'tehpucuk.webp',
                'dibuat_pada' => '2025-11-02 01:32:01',
            ],
        ]);
    }
}