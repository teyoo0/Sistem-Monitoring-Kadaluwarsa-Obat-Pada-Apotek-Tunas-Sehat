<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Riwayat Pemeriksaan';
$keyword = trim($_GET['q'] ?? '');
$filterStatus = $_GET['filter'] ?? '';

$sql = "
    SELECT p.*, b.nomor_batch, o.nama_obat, o.kode_obat, u.nama_lengkap
    FROM pemeriksaan p
    JOIN batch b ON b.id_batch = p.id_batch
    JOIN obat o ON o.id_obat = b.id_obat
    JOIN users u ON u.id_user = p.id_user
    WHERE 1=1
";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (o.nama_obat LIKE ? OR b.nomor_batch LIKE ?)";
    $like = "%$keyword%";
    $params[] = $like;
    $params[] = $like;
}
if ($filterStatus !== '') {
    $sql .= " AND p.status_saat_diperiksa = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

$statusOptions = ['Aman', 'Perhatian', 'Kritis', 'Kedaluwarsa'];

include __DIR__ . '/../../includes/header.php';
?>

<div class="panel">
  <form method="GET" class="d-flex gap-2 flex-wrap mb-3">
    <input type="text" name="q" class="form-control" placeholder="Cari nama obat atau nomor batch..." value="<?= e($keyword) ?>" style="min-width:230px;">
    <select name="filter" class="form-select" style="width:auto;">
      <option value="">Semua Status</option>
      <?php foreach ($statusOptions as $s): ?>
        <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline-secondary" type="submit">Terapkan</button>
    <?php if ($keyword !== '' || $filterStatus !== ''): ?>
      <a href="index.php" class="btn btn-outline-secondary">Reset</a>
    <?php endif; ?>
  </form>

  <?php if (empty($list)): ?>
    <div class="empty-state"><div class="icon">&#128220;</div>Belum ada riwayat pemeriksaan yang cocok.</div>
  <?php else: ?>
  <div class="table-responsive-wrap">
    <table class="table-app">
      <thead>
        <tr><th>Tanggal</th><th>Obat</th><th>Batch</th><th>Status Saat Diperiksa</th><th>Hasil</th><th>Tindakan</th><th>Petugas</th><th>Catatan</th></tr>
      </thead>
      <tbody>
      <?php foreach ($list as $p): ?>
        <tr>
          <td><?= formatTanggalIndo($p['tanggal_pemeriksaan']) ?></td>
          <td><?= e($p['nama_obat']) ?> <span class="text-muted small">(<?= e($p['kode_obat']) ?>)</span></td>
          <td><?= e($p['nomor_batch']) ?></td>
          <td><span class="badge-status <?= statusBadgeClass($p['status_saat_diperiksa']) ?>"><?= e($p['status_saat_diperiksa']) ?></span></td>
          <td><?= e($p['hasil_pemeriksaan']) ?></td>
          <td><?= e($p['tindakan']) ?></td>
          <td><?= e($p['nama_lengkap']) ?></td>
          <td class="text-muted small"><?= e($p['catatan'] ?: '-') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
