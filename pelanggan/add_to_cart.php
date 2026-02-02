<?php
require '../config.php';
require_login();

$id_user = $_SESSION['user_id'];
$id_menu = isset($_POST['id_menu']) ? (int)$_POST['id_menu'] : 0;
$jumlah  = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

if ($id_menu <= 0 || $jumlah <= 0) {
    header("Location: index.php");
    exit;
}

// cek apakah sudah ada di keranjang -> update jumlah
$stmt = $pdo->prepare("SELECT * FROM keranjang WHERE id_user = ? AND id_menu = ?");
$stmt->execute([$id_user, $id_menu]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare("UPDATE keranjang SET jumlah = jumlah + ? WHERE id_keranjang = ?");
    $stmt->execute([$jumlah, $existing['id_keranjang']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO keranjang (id_user, id_menu, jumlah) VALUES (?, ?, ?)");
    $stmt->execute([$id_user, $id_menu, $jumlah]);
}

header("Location: index.php");
exit;