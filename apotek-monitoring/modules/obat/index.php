<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Data Obat';
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM obat WHERE nama_obat LIKE ? OR kode_obat LIKE ? ORDER BY nama_obat ASC");
    $like = "%$keyword%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM obat ORDER BY nama_obat ASC");
}
$obatList = $stmt->fetchAll();

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
      <input type="text" name="q" class="form-control" placeholder="Cari nama atau kode obat..." value="<?= e($keyword) ?>" style="min-width:250px;">
      <button class="btn btn-outline-secondary" type="submit">Cari</button>
    </form>
    <a href="create.php" class="btn btn-primary-app text-white">+ Tambah Obat</a>
  </div>

  <?php if (empty($obatList)): ?>
    <div class="empty-state"><div class="icon">&#128138;</div>Belum ada data obat.</div>
  <?php else: ?>
  <div class="table-responsive-wrap">
    <table class="table-app">
      <thead>
        <tr><th>Kode</th><th>Nama Obat</th><th>Kategori</th><th>Satuan</th><th>Keterangan</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($obatList as $o): ?>
        <tr>
          <td><?= e($o['kode_obat']) ?></td>
          <td><?= e($o['nama_obat']) ?></td>
          <td><?= e($o['kategori']) ?></td>
          <td><?= e($o['satuan']) ?></td>
          <td class="text-muted small"><?= e($o['keterangan'] ?: '-') ?></td>
          <td class="d-flex gap-2">
            <a href="edit.php?id=<?= $o['id_obat'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
            <a href="delete.php?id=<?= $o['id_obat'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus obat ini? Semua batch terkait harus dihapus dulu.');">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
