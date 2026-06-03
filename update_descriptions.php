<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Menu;

$menus = Menu::all();
$updatedCount = 0;

$descriptions = [
    'Ayam Geprek' => 'Ayam goreng krispi yang digeprek dengan sambal bawang pedas nampol. Disajikan dengan nasi hangat dan lalapan.',
    'Ayam Crispy' => 'Ayam goreng dengan balutan tepung rahasia yang super renyah dan gurih di setiap gigitan.',
    'Es Teh' => 'Teh manis dingin yang segar, cocok untuk menemani hidangan pedas.',
    'Es Jeruk' => 'Perasan jeruk asli yang segar dan kaya vitamin C, disajikan dingin.',
    'Nasi Putih' => 'Nasi putih pulen berkualitas untuk pelengkap hidangan utama.',
    'Paket Hemat' => 'Kombinasi nasi, ayam geprek, dan es teh dengan harga lebih ekonomis.',
    'Sambal Bawang' => 'Sambal bawang khas MFC yang pedas, gurih, dan menggugah selera.',
    'Kol Goreng' => 'Kol segar yang digoreng garing, memberikan sensasi rasa manis dan gurih.',
];

foreach ($menus as $menu) {
    if (empty($menu->deskripsi) || strlen($menu->deskripsi) < 5) {
        foreach ($descriptions as $keyword => $desc) {
            if (stripos($menu->nama, $keyword) !== false) {
                $menu->deskripsi = $desc;
                $menu->save();
                $updatedCount++;
                break;
            }
        }
    }
}

echo "Berhasil memperbarui $updatedCount deskripsi menu.\n";
