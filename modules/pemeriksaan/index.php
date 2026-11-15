<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Pemeriksaan & Tindakan';

$stmt = $pdo->query("
    SELECT p.*, b.nomor_batch, o.nama_obat, u.nama_lengkap
    FROM pemeriksaan p
    JOIN batch b ON b.id_batch = p.id_batch
    JOIN obat o ON o.id_obat = b.id_obat
    JOIN users u ON u.id_user = p.id_user
    ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
    LIMIT 20
");
$list = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success" data-autohide><?= e($_GET['success']) ?></div>
<?php endif; ?>

<div class="panel">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">Menampilkan 20 pemeriksaan terbaru. Untuk pencarian lengkap, buka menu Riwayat.</div>
    <a href="../monitoring/index.php" class="btn btn-primary-app text-white">Pilih Batch untuk Diperiksa</a>
  </div>

  <?php if (empty($list)): ?>
    <div class="empty-state"><div class="icon">&#9989;</div>Belum ada data pemeriksaan.</div>
  <?php else: ?>
  <div class="table-responsive-wrap">
    <table class="table-app">
      <thead>
        <tr><th>Tanggal</th><th>Obat</th><th>Batch</th><th>Status Saat Diperiksa</th><th>Hasil</th><th>Tindakan</th><th>Petugas</th></tr>
      </thead>
      <tbody>
      <?php foreach ($list as $p): ?>
        <tr>
          <td><?= formatTanggalIndo($p['tanggal_pemeriksaan']) ?></td>
          <td><?= e($p['nama_obat']) ?></td>
          <td><?= e($p['nomor_batch']) ?></td>
          <td><span class="badge-status <?= statusBadgeClass($p['status_saat_diperiksa']) ?>"><?= e($p['status_saat_diperiksa']) ?></span></td>
          <td><?= e($p['hasil_pemeriksaan']) ?></td>
          <td><?= e($p['tindakan']) ?></td>
          <td><?= e($p['nama_lengkap']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
