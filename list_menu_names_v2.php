<?php
$dsn = "mysql:host=127.0.0.1;port=3307;dbname=mfc;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, 'root', '');
    $stmt = $pdo->query("SELECT nama, deskripsi FROM menu");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($menus, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
