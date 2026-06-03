<?php
$env = parse_ini_file('.env');
$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD']);
    $stmt = $pdo->query("SELECT nama, deskripsi FROM menu");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($menus, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
