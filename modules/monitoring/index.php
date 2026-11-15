<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Monitoring Kedaluwarsa';
$filterStatus = $_GET['filter'] ?? '';
$keyword = trim($_GET['q'] ?? '');

$sql = "SELECT b.*, o.nama_obat, o.kode_obat FROM batch b JOIN obat o ON o.id_obat = b.id_obat";
$params = [];
if ($keyword !== '') {
    $sql .= " WHERE o.nama_obat LIKE ? OR b.nomor_batch LIKE ?";
    $like = "%$keyword%";
    $params = [$like, $like];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$batchList = $stmt->fetchAll();

foreach ($batchList as &$b) {
    $hasil = hitungStatusBatch($b['tanggal_kedaluwarsa']);
    $b['sisa_hari'] = $hasil['sisa_hari'];
    $b['status']    = $hasil['status'];
}
unset($b);

if ($filterStatus !== '') {
    $batchList = array_values(array_filter($batchList, fn($b) => $b['status'] === $filterStatus));
}

usort($batchList, fn($a, $b) => $a['sisa_hari'] <=> $b['sisa_hari']);

$statusOptions = ['Aman', 'Perhatian', 'Kritis', 'Kedaluwarsa'];

include __DIR__ . '/../../includes/header.php';
?>

<div class="panel">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap">
      <input type="text" name="q" class="form-control" placeholder="Cari nama obat atau nomor batch..." value="<?= e($keyword) ?>" style="min-width:230px;">
      <select name="filter" class="form-select" style="width:auto;">
        <option value="">Semua Status</option>
        <?php foreach ($statusOptions as $s): ?>
          <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-outline-secondary" type="submit">Terapkan</button>
      <?php if ($filterStatus !== '' || $keyword !== ''): ?>
        <a href="index.php" class="btn btn-outline-secondary">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <?php foreach ($statusOptions as $s): ?>
      <a href="?filter=<?= $s ?>" class="badge-status <?= statusBadgeClass($s) ?>" style="text-decoration:none; <?= $filterStatus === $s ? 'box-shadow:0 0 0 2px #111827;' : '' ?>"><?= $s ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($batchList)): ?>
    <div class="empty-state"><div class="icon">&#128269;</div>Tidak ada batch yang cocok dengan filter/pencarian.</div>
  <?php else: ?>
  <div class="table-responsive-wrap">
    <table class="table-app">
      <thead>
        <tr><th>Nama Obat</th><th>Batch</th><th>Tanggal Kedaluwarsa</th><th>Sisa Hari</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($batchList as $b): ?>
        <tr>
          <td><?= e($b['nama_obat']) ?> <span class="text-muted small">(<?= e($b['kode_obat']) ?>)</span></td>
          <td><?= e($b['nomor_batch']) ?></td>
          <td><?= formatTanggalIndo($b['tanggal_kedaluwarsa']) ?></td>
          <td><?= labelSisaHari($b['sisa_hari']) ?></td>
          <td><span class="badge-status <?= statusBadgeClass($b['status']) ?>"><?= $b['status'] ?></span></td>
          <td>
            <a href="../pemeriksaan/create.php?id_batch=<?= $b['id_batch'] ?>" class="btn btn-sm btn-primary-app text-white">Catat Pemeriksaan</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
