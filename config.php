<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===== DATABASE ===== */
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? '';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

/* ===== AUTH HELPERS ===== */
if (!function_exists('require_login')) {
    function require_login()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /restoran/login.php');
            exit;
        }
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

if (!function_exists('require_admin')) {
    function require_admin()
    {
        require_login();
        if (!is_admin()) {
            header('Location: /restoran/index.php');
            exit;
        }
    }
}

/* ===== MIDTRANS CONFIG ===== */
$serverKey = $_ENV['MIDTRANS_SERVER_KEY'] ?? '';
$clientKey = $_ENV['MIDTRANS_CLIENT_KEY'] ?? '';
$isProdRaw = $_ENV['MIDTRANS_IS_PRODUCTION'] ?? 'false';
$isProduction = filter_var($isProdRaw, FILTER_VALIDATE_BOOLEAN); // "false" => false, "true" => true

\Midtrans\Config::$serverKey    = $serverKey;
\Midtrans\Config::$isProduction = $isProduction;

// Biar bisa dipakai di file lain
define('MIDTRANS_SERVER_KEY', $serverKey);
define('MIDTRANS_CLIENT_KEY', $clientKey);
define('MIDTRANS_IS_PRODUCTION', $isProduction);

define(
    'MIDTRANS_API_URL',
    MIDTRANS_IS_PRODUCTION
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions'
);