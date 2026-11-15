<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Edit Obat';
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM obat WHERE id_obat = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

if (!$obat) {
    header('Location: index.php?error=' . urlencode('Data obat tidak ditemukan.'));
    exit;
}

$errors = [];
$old = $obat;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'kode_obat'  => trim($_POST['kode_obat'] ?? ''),
        'nama_obat'  => trim($_POST['nama_obat'] ?? ''),
        'kategori'   => trim($_POST['kategori'] ?? ''),
        'satuan'     => trim($_POST['satuan'] ?? ''),
        'keterangan' => trim($_POST['keterangan'] ?? ''),
    ];

    if ($old['kode_obat'] === '') $errors[] = 'Kode obat wajib diisi.';
    if ($old['nama_obat'] === '') $errors[] = 'Nama obat wajib diisi.';
    if ($old['kategori'] === '') $errors[] = 'Kategori wajib diisi.';
    if ($old['satuan'] === '') $errors[] = 'Satuan wajib diisi.';

    if (empty($errors)) {
        $cek = $pdo->prepare("SELECT COUNT(*) c FROM obat WHERE kode_obat = ? AND id_obat != ?");
        $cek->execute([$old['kode_obat'], $id]);
        if ($cek->fetch()['c'] > 0) {
            $errors[] = 'Kode obat sudah digunakan oleh obat lain.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE obat SET kode_obat=?, nama_obat=?, kategori=?, satuan=?, keterangan=? WHERE id_obat=?");
        $stmt->execute([$old['kode_obat'], $old['nama_obat'], $old['kategori'], $old['satuan'], $old['keterangan'] ?: null, $id]);
        header('Location: index.php?success=' . urlencode('Data obat berhasil diperbarui.'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="panel" style="max-width:640px;">
  <?php foreach ($errors as $err): ?>
    <div class="alert alert-danger py-2"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label class="form-label">Kode Obat <span class="required-mark">*</span></label>
      <input type="text" name="kode_obat" class="form-control" value="<?= e($old['kode_obat']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Nama Obat <span class="required-mark">*</span></label>
      <input type="text" name="nama_obat" class="form-control" value="<?= e($old['nama_obat']) ?>" required>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Kategori <span class="required-mark">*</span></label>
        <input type="text" name="kategori" class="form-control" value="<?= e($old['kategori']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Satuan <span class="required-mark">*</span></label>
        <input type="text" name="satuan" class="form-control" value="<?= e($old['satuan']) ?>" required>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Keterangan</label>
      <textarea name="keterangan" class="form-control" rows="3"><?= e($old['keterangan']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary-app text-white">Perbarui</button>
    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
  </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
