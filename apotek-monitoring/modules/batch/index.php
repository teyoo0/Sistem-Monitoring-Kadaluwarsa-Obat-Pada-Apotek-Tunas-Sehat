<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Data Batch';
$keyword = trim($_GET['q'] ?? '');

$sql = "SELECT b.*, o.nama_obat, o.kode_obat FROM batch b JOIN obat o ON o.id_obat = b.id_obat";
$params = [];
if ($keyword !== '') {
    $sql .= " WHERE o.nama_obat LIKE ? OR b.nomor_batch LIKE ?";
    $like = "%$keyword%";
    $params = [$like, $like];
}
$sql .= " ORDER BY b.tanggal_kedaluwarsa ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$batchList = $stmt->fetchAll();

foreach ($batchList as &$b) {
    $hasil = hitungStatusBatch($b['tanggal_kedaluwarsa']);
    $b['sisa_hari'] = $hasil['sisa_hari'];
    $b['status']    = $hasil['status'];
}
unset($b);

// Daftar obat untuk dropdown pilih saat tambah (dipakai juga di create.php, tapi query di sini utk info saja)
include __DIR__ . '/../../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success" data-autohide><?= e($_GET['success']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger" data-autohide><?= e($_GET['error']) ?></div>
<?php endif; ?>

<div class="panel">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <form method="GET" class="d-flex gap-2">
      <input type="text" name="q" class="form-control" placeholder="Cari nama obat atau nomor batch..." value="<?= e($keyword) ?>" style="min-width:250px;">
      <button class="btn btn-outline-secondary" type="submit">Cari</button>
    </form>
    <a href="create.php" class="btn btn-primary-app text-white">+ Tambah Batch</a>
  </div>

  <?php if (empty($batchList)): ?>
    <div class="empty-state"><div class="icon">&#128230;</div>Belum ada data batch.</div>
  <?php else: ?>
  <div class="table-responsive-wrap">
    <table class="table-app">
      <thead>
        <tr><th>Nama Obat</th><th>Batch</th><th>Tanggal Kedaluwarsa</th><th>Sisa Hari</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($batchList as $b): ?>
        <tr>
          <td><?= e($b['nama_obat']) ?> <span class="text-muted small">(<?= e($b['kode_obat']) ?>)</span></td>
          <td><?= e($b['nomor_batch']) ?></td>
          <td><?= formatTanggalIndo($b['tanggal_kedaluwarsa']) ?></td>
          <td><?= labelSisaHari($b['sisa_hari']) ?></td>
          <td><?= (int) $b['jumlah_stok'] ?></td>
          <td><span class="badge-status <?= statusBadgeClass($b['status']) ?>"><?= $b['status'] ?></span></td>
          <td class="d-flex gap-2">
            <a href="edit.php?id=<?= $b['id_batch'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
            <a href="delete.php?id=<?= $b['id_batch'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus batch ini? Riwayat pemeriksaan terkait juga akan terhapus.');">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
