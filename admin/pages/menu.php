<?php
// require_once __DIR__ . '/../../config.php';
// require_admin();

/* ==========================
   HELPER UPLOAD GAMBAR
========================== */
function upload_gambar($field, $oldFile = null)
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldFile;
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return $oldFile;
    }

    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) return $oldFile;

    $newName = 'menu_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $path = __DIR__ . '/../../assets/img/' . $newName;

    if (move_uploaded_file($_FILES[$field]['tmp_name'], $path)) {
        return $newName;
    }

    return $oldFile;
}

/* ==========================
   DELETE MENU (POST ONLY)
========================== */
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];

    $st = $pdo->prepare("SELECT gambar FROM menu WHERE id_menu=?");
    $st->execute([$id]);
    $gambar = $st->fetchColumn();

    $pdo->prepare("DELETE FROM menu WHERE id_menu=?")->execute([$id]);

    if ($gambar) {
        $file = __DIR__ . '/../../assets/img/' . $gambar;
        if (is_file($file)) unlink($file);
    }

    // header("Location: index.php?page=menu&msg=deleted");
    // exit;
}

/* ==========================
   SIMPAN (INSERT / UPDATE)
========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama'])) {

    $id   = (int)($_POST['id_menu'] ?? 0);
    $nama = trim($_POST['nama']);
    $kat  = $_POST['kategori'];
    $hrg  = (int)$_POST['harga'];
    $desk = trim($_POST['deskripsi']);

    $oldImg = null;
    if ($id > 0) {
        $st = $pdo->prepare("SELECT gambar FROM menu WHERE id_menu=?");
        $st->execute([$id]);
        $oldImg = $st->fetchColumn();
    }

    $gambar = upload_gambar('gambar', $oldImg);

    if ($id > 0) {
        $pdo->prepare("
            UPDATE menu 
            SET nama=?, kategori=?, harga=?, deskripsi=?, gambar=?
            WHERE id_menu=?
        ")->execute([$nama,$kat,$hrg,$desk,$gambar,$id]);

        // header("Location: index.php?page=menu&msg=updated");
        // exit;

    } else {
        $pdo->prepare("
            INSERT INTO menu (nama,kategori,harga,deskripsi,gambar)
            VALUES (?,?,?,?,?)
        ")->execute([$nama,$kat,$hrg,$desk,$gambar]);

        // header("Location: index.php?page=menu&msg=saved");
        // exit;
    }
}

/* ==========================
   DATA MENU
========================== */
$menus = $pdo->query("SELECT * FROM menu ORDER BY id_menu DESC")->fetchAll();

$msg = $_GET['msg'] ?? '';
include __DIR__ . '/../partials/admin_header.php';
?>

<div class="container py-4">

<h3 class="fw-bold mb-3">Kelola Menu</h3>

<?php if($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
<?= $msg=='saved'?'Menu ditambahkan':($msg=='updated'?'Menu diupdate':'Menu dihapus') ?>
<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- FORM TAMBAH -->
<div class="card mb-4 shadow-sm">
<div class="card-body">
<h5 class="fw-bold mb-3">Tambah Menu</h5>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id_menu" value="0">

<div class="mb-3">
<label>Nama</label>
<input name="nama" class="form-control" required>
</div>

<div class="mb-3">
<label>Kategori</label>
<select name="kategori" class="form-select">
<option value="makanan">Makanan</option>
<option value="minuman">Minuman</option>
<option value="tambahan">Tambahan</option>
</select>
</div>

<div class="mb-3">
<label>Harga</label>
<input type="number" name="harga" class="form-control" required>
</div>

<div class="mb-3">
<label>Deskripsi</label>
<textarea name="deskripsi" class="form-control"></textarea>
</div>

<div class="mb-3">
<label>Gambar</label>
<input type="file" name="gambar" class="form-control">
</div>

<button class="btn btn-main text-white rounded-pill">Tambah Menu</button>
</form>
</div>
</div>

<!-- LIST MENU -->
<div class="card shadow-sm">
<div class="card-body">
<h5 class="fw-bold mb-3">Daftar Menu</h5>

<table class="table table-hover align-middle">
<thead>
<tr>
<th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Aksi</th>
</tr>
</thead>
<tbody>

<?php foreach($menus as $m): ?>
<tr>
<td>
<?php if($m['gambar']): ?>
<img src="../assets/img/<?= $m['gambar'] ?>" width="50" class="rounded">
<?php endif; ?>
</td>

<td><?= htmlspecialchars($m['nama']) ?></td>
<td><?= ucfirst($m['kategori']) ?></td>
<td>Rp <?= number_format($m['harga'],0,',','.') ?></td>

<td class="text-nowrap">
<button class="btn btn-sm btn-outline-primary btn-edit"
data-id="<?= $m['id_menu'] ?>"
data-nama="<?= htmlspecialchars($m['nama']) ?>"
data-kategori="<?= $m['kategori'] ?>"
data-harga="<?= $m['harga'] ?>"
data-deskripsi="<?= htmlspecialchars($m['deskripsi']) ?>"
data-bs-toggle="modal"
data-bs-target="#modalEdit">
Edit</button>

<form method="post" class="d-inline">
<input type="hidden" name="delete_id" value="<?= $m['id_menu'] ?>">
<button class="btn btn-sm btn-outline-danger"
onclick="return confirm('Hapus menu ini?')">Hapus</button>
</form>
</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
</div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" enctype="multipart/form-data">

<div class="modal-header">
<h5>Edit Menu</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="id_menu" id="eid">

<input class="form-control mb-2" name="nama" id="enama" required>
<select class="form-select mb-2" name="kategori" id="ekat">
<option value="makanan">Makanan</option>
<option value="minuman">Minuman</option>
<option value="tambahan">Tambahan</option>
</select>
<input type="number" class="form-control mb-2" name="harga" id="eharga">
<textarea class="form-control mb-2" name="deskripsi" id="edesk"></textarea>
<input type="file" name="gambar" class="form-control">
</div>

<div class="modal-footer">
<button class="btn btn-main text-white rounded-pill">Update</button>
</div>

</form>
</div>
</div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(b=>{
b.onclick=()=>{
eid.value=b.dataset.id;
enama.value=b.dataset.nama;
ekat.value=b.dataset.kategori;
eharga.value=b.dataset.harga;
edesk.value=b.dataset.deskripsi;
}
});
</script>