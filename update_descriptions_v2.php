<?php
$dsn = "mysql:host=127.0.0.1;port=3307;dbname=mfc;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, 'root', '');
    
    $updates = [
        'PAKET HAJAT' => 'Paket lengkap kenyang! Nasi putih hangat disajikan dengan Dada Ayam Jumbo krispi, tahu goreng, dan lalapan segar.',
        'PAKET HEMAT 1' => 'Pilihan ekonomis yang tetap nikmat. Nasi, Sayap Ayam renyah, tahu, tempe, dan lalapan.',
        'PAKET HEMAT 2' => 'Perpaduan sempurna Nasi, Paha Ayam krispi, tahu tempe, lalapan, lengkap dengan kesegaran Es Teh Manis.',
        'PAKET HEMAT 3' => 'Menu favorit! Nasi dengan Dada Ayam krispi, tahu tempe, lalapan, dan segelas Es Teh Manis dingin.',
        'LELE CRISPY' => 'Ikan lele segar yang digoreng dengan tepung krispi rahasia hingga garing di luar dan lembut di dalam.',
        'SAYAP AYAM' => 'Sayap ayam goreng tepung dengan bumbu rempah meresap dan tekstur yang sangat renyah.',
        'PAHA AYAM' => 'Paha ayam goreng krispi yang juicy dengan kulit yang gurih dan garing.',
        'DADA AYAM' => 'Dada ayam goreng krispi rendah lemak namun tetap gurih dan renyah maksimal.',
        'DADA AYAM JUMBO' => 'Porsi lebih besar! Dada ayam pilihan dengan ukuran jumbo yang digoreng krispi sempurna.',
        'TAHU CRISPY' => 'Tahu goreng yang dibalut tepung krispi gurih. Cocok untuk cemilan atau pelengkap makan.',
        'USUS CRISPY' => 'Usus ayam pilihan yang digoreng hingga sangat garing dan kriuk. Gurihnya bikin nagih!',
        'ES TELER' => 'Minuman segar dengan perpaduan nangka, alpukat, kelapa muda, dan susu kental manis yang melimpah.',
        'ES TEH' => 'Teh seduh berkualitas yang disajikan dingin dengan gula asli. Segar seketika!',
        'Nasi' => 'Nasi putih hangat yang pulen, pelengkap utama santapan ayam krispi Anda.'
    ];

    $stmt = $pdo->prepare("UPDATE menu SET deskripsi = ? WHERE nama = ?");
    
    $count = 0;
    foreach ($updates as $nama => $deskripsi) {
        if ($stmt->execute([$deskripsi, $nama])) {
            if ($stmt->rowCount() > 0) {
                $count++;
            }
        }
    }
    
    echo "Berhasil memperbarui $count deskripsi menu.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
