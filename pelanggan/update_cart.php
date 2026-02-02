<?php
require '../config.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id_user = $_SESSION['user_id'];
$id_menu = isset($_POST['id_menu']) ? (int)$_POST['id_menu'] : 0;
$action  = $_POST['action'] ?? '';

if ($id_menu <= 0 || !in_array($action, ['plus', 'minus', 'set'], true)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM keranjang WHERE id_user = ? AND id_menu = ?");
$stmt->execute([$id_user, $id_menu]);
$row = $stmt->fetch();

$newQty = 0;

if ($action === 'set') {
    $newQty = max(0, (int)($_POST['value'] ?? 0));

    if ($newQty > 0) {
        if ($row) {
            $stmtUpdate = $pdo->prepare("UPDATE keranjang SET jumlah = ? WHERE id_keranjang = ?");
            $stmtUpdate->execute([$newQty, $row['id_keranjang']]);
        } else {
            $stmtIns = $pdo->prepare("INSERT INTO keranjang (id_user, id_menu, jumlah) VALUES (?, ?, ?)");
            $stmtIns->execute([$id_user, $id_menu, $newQty]);
        }
    } else {
        if ($row) {
            $stmtDel = $pdo->prepare("DELETE FROM keranjang WHERE id_keranjang = ?");
            $stmtDel->execute([$row['id_keranjang']]);
        }
        $newQty = 0;
    }
}

if (!$row) {
    if ($action === 'plus') {
        $stmtIns = $pdo->prepare("INSERT INTO keranjang (id_user, id_menu, jumlah) VALUES (?, ?, 1)");
        $stmtIns->execute([$id_user, $id_menu]);
        $newQty = 1;
    } else {
        echo json_encode(['success' => false, 'message' => 'Item belum ada di keranjang']);
        exit;
    }
} else {
    $newQty = (int)$row['jumlah'];

    if ($action === 'plus') {
        $newQty++;
    } elseif ($action === 'minus') {
        $newQty--;
    }

    if ($newQty > 0) {
        $stmtUpdate = $pdo->prepare("UPDATE keranjang SET jumlah = ? WHERE id_keranjang = ?");
        $stmtUpdate->execute([$newQty, $row['id_keranjang']]);
    } else {
        $stmtDel = $pdo->prepare("DELETE FROM keranjang WHERE id_keranjang = ?");
        $stmtDel->execute([$row['id_keranjang']]);
        $newQty = 0;
    }
}

$stmtTotalItem = $pdo->prepare("SELECT SUM(jumlah) AS total_item FROM keranjang WHERE id_user = ?");
$stmtTotalItem->execute([$id_user]);
$totalRow    = $stmtTotalItem->fetch();
$cart_count  = (int)($totalRow['total_item'] ?? 0);

$stmtTotalHarga = $pdo->prepare("
    SELECT SUM(k.jumlah * m.harga) AS total_harga
    FROM keranjang k
    JOIN menu m ON m.id_menu = k.id_menu
    WHERE k.id_user = ?
");
$stmtTotalHarga->execute([$id_user]);
$rowTotal    = $stmtTotalHarga->fetch();
$cart_total  = (int)($rowTotal['total_harga'] ?? 0);

$item_subtotal = 0;
if ($newQty > 0) {
    $stmtItem = $pdo->prepare("
        SELECT k.jumlah, m.harga
        FROM keranjang k
        JOIN menu m ON m.id_menu = k.id_menu
        WHERE k.id_user = ? AND k.id_menu = ?
    ");
    $stmtItem->execute([$id_user, $id_menu]);
    $rowItem = $stmtItem->fetch();
    if ($rowItem) {
        $item_subtotal = (int)$rowItem['jumlah'] * (int)$rowItem['harga'];
    }
}

echo json_encode([
    'success'       => true,
    'new_qty'       => $newQty,
    'item_subtotal' => $item_subtotal, 
    'cart_total'    => $cart_total,   
    'cart_count'    => $cart_count   
]);