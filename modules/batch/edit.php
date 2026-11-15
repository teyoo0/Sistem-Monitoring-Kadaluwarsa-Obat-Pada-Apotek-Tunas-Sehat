<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Edit Batch';
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM batch WHERE id_batch = ?");
$stmt->execute([$id]);
$batch = $stmt->fetch();

if (!$batch) {
    header('Location: index.php?error=' . urlencode('Data batch tidak ditemukan.'));
    exit;
}

$obatOptions = $pdo->query("SELECT id_obat, nama_obat, kode_obat FROM obat ORDER BY nama_obat ASC")->fetchAll();
$errors = [];
$old = $batch;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'id_obat'             => (int) ($_POST['id_obat'] ?? 0),
        'nomor_batch'         => trim($_POST['nomor_batch'] ?? ''),
        'tanggal_masuk'       => trim($_POST['tanggal_masuk'] ?? ''),
        'tanggal_kedaluwarsa' => trim($_POST['tanggal_kedaluwarsa'] ?? ''),
        'jumlah_stok'         => trim($_POST['jumlah_stok'] ?? ''),
    ];

    if ($old['id_obat'] <= 0) $errors[] = 'Obat wajib dipilih.';
    if ($old['nomor_batch'] === '') $errors[] = 'Nomor batch wajib diisi.';
    if ($old['tanggal_masuk'] === '') $errors[] = 'Tanggal masuk wajib diisi.';
    if ($old['tanggal_kedaluwarsa'] === '') {
        $errors[] = 'Tanggal kedaluwarsa wajib diisi.';
    } elseif (!DateTime::createFromFormat('Y-m-d', $old['tanggal_kedaluwarsa'])) {
        $errors[] = 'Format tanggal kedaluwarsa tidak valid.';
    }
    if ($old['jumlah_stok'] === '' || !is_numeric($old['jumlah_stok']) || (int)$old['jumlah_stok'] < 0) {
        $errors[] = 'Jumlah stok harus berupa angka dan tidak boleh negatif.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE batch SET id_obat=?, nomor_batch=?, tanggal_masuk=?, tanggal_kedaluwarsa=?, jumlah_stok=? WHERE id_batch=?");
        $stmt->execute([$old['id_obat'], $old['nomor_batch'], $old['tanggal_masuk'], $old['tanggal_kedaluwarsa'], (int) $old['jumlah_stok'], $id]);
        header('Location: index.php?success=' . urlencode('Data batch berhasil diperbarui.'));
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
      <label class="form-label">Obat <span class="required-mark">*</span></label>
      <select name="id_obat" class="form-select" required>
        <?php foreach ($obatOptions as $o): ?>
          <option value="<?= $o['id_obat'] ?>" <?= ((int)$old['id_obat'] === (int)$o['id_obat']) ? 'selected' : '' ?>>
            <?= e($o['nama_obat']) ?> (<?= e($o['kode_obat']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Nomor Batch <span class="required-mark">*</span></label>
      <input type="text" name="nomor_batch" class="form-control" value="<?= e($old['nomor_batch']) ?>" required>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Masuk <span class="required-mark">*</span></label>
        <input type="date" name="tanggal_masuk" class="form-control" value="<?= e($old['tanggal_masuk']) ?>" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Kedaluwarsa <span class="required-mark">*</span></label>
        <input type="date" name="tanggal_kedaluwarsa" class="form-control" value="<?= e($old['tanggal_kedaluwarsa']) ?>" required>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Jumlah Stok <span class="required-mark">*</span></label>
      <input type="number" min="0" name="jumlah_stok" class="form-control" value="<?= e((string)$old['jumlah_stok']) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary-app text-white">Perbarui</button>
    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
  </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
