<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Dashboard';

// Ambil seluruh batch beserta nama obat untuk dihitung statusnya
$stmt = $pdo->query("
    SELECT b.*, o.nama_obat, o.kode_obat
    FROM batch b
    JOIN obat o ON o.id_obat = b.id_obat
");
$allBatch = $stmt->fetchAll();

$countStatus = ['Aman' => 0, 'Perhatian' => 0, 'Kritis' => 0, 'Kedaluwarsa' => 0];
foreach ($allBatch as &$b) {
    $hasil = hitungStatusBatch($b['tanggal_kedaluwarsa']);
    $b['sisa_hari'] = $hasil['sisa_hari'];
    $b['status']    = $hasil['status'];
    $countStatus[$hasil['status']]++;
}
unset($b);

// Urutkan berdasarkan sisa hari (paling dekat kedaluwarsa dahulu)
usort($allBatch, fn($a, $b) => $a['sisa_hari'] <=> $b['sisa_hari']);

$totalObat  = (int) $pdo->query("SELECT COUNT(*) c FROM obat")->fetch()['c'];
$totalBatch = count($allBatch);

$batchPerluPerhatian = array_slice($allBatch, 0, 8); // 8 batch paling dekat kedaluwarsa
$batchKritis = array_values(array_filter($allBatch, fn($b) => $b['status'] === 'Kritis'));
$batchExpired = array_values(array_filter($allBatch, fn($b) => $b['status'] === 'Kedaluwarsa'));

include __DIR__ . '/../../includes/header.php';
?>

<div class="row g-3 mb-2">
  <div class="col-6 col-lg-2">
    <div class="stat-card">
      <div class="stat-label">Total Obat</div>
      <div class="stat-value"><?= $totalObat ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-2">
    <div class="stat-card">
      <div class="stat-label">Total Batch</div>
      <div class="stat-value"><?= $totalBatch ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-2">
    <div class="stat-card accent-aman">
      <div class="stat-label">Batch Aman</div>
      <div class="stat-value"><?= $countStatus['Aman'] ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-2">
    <div class="stat-card accent-perhatian">
      <div class="stat-label">Batch Perhatian</div>
      <div class="stat-value"><?= $countStatus['Perhatian'] ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-2">
    <div class="stat-card accent-kritis">
      <div class="stat-label">Batch Kritis</div>
      <div class="stat-value"><?= $countStatus['Kritis'] ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-2">
    <div class="stat-card accent-kedaluwarsa">
      <div class="stat-label">Batch Kedaluwarsa</div>
      <div class="stat-value"><?= $countStatus['Kedaluwarsa'] ?></div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-title">Batch Paling Dekat Kedaluwarsa</div>
      <?php if (empty($batchPerluPerhatian)): ?>
        <div class="empty-state"><div class="icon">&#128522;</div>Belum ada data batch.</div>
      <?php else: ?>
      <div class="table-responsive-wrap">
        <table class="table-app">
          <thead><tr><th>Obat</th><th>Batch</th><th>Kedaluwarsa</th><th>Sisa Hari</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($batchPerluPerhatian as $b): ?>
            <tr>
              <td><?= e($b['nama_obat']) ?></td>
              <td><?= e($b['nomor_batch']) ?></td>
              <td><?= formatTanggalIndo($b['tanggal_kedaluwarsa']) ?></td>
              <td><?= labelSisaHari($b['sisa_hari']) ?></td>
              <td><span class="badge-status <?= statusBadgeClass($b['status']) ?>"><?= $b['status'] ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <div class="panel">
      <div class="panel-title">Batch Sudah Kedaluwarsa</div>
      <?php if (empty($batchExpired)): ?>
        <div class="empty-state"><div class="icon">&#9989;</div>Tidak ada batch kedaluwarsa saat ini.</div>
      <?php else: ?>
      <div class="table-responsive-wrap">
        <table class="table-app">
          <thead><tr><th>Obat</th><th>Batch</th><th>Kedaluwarsa</th><th>Terlewat</th></tr></thead>
          <tbody>
          <?php foreach ($batchExpired as $b): ?>
            <tr>
              <td><?= e($b['nama_obat']) ?></td>
              <td><?= e($b['nomor_batch']) ?></td>
              <td><?= formatTanggalIndo($b['tanggal_kedaluwarsa']) ?></td>
              <td><?= labelSisaHari($b['sisa_hari']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-title">Grafik Status Kedaluwarsa</div>
      <canvas id="chartStatus" height="220"></canvas>
    </div>

    <div class="panel">
      <div class="panel-title">Batch Kritis (perlu tindakan segera)</div>
      <?php if (empty($batchKritis)): ?>
        <div class="empty-state"><div class="icon">&#128077;</div>Tidak ada batch kritis.</div>
      <?php else: ?>
        <?php foreach ($batchKritis as $b): ?>
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <div>
              <div class="fw-semibold small"><?= e($b['nama_obat']) ?></div>
              <div class="text-muted" style="font-size:12px;">Batch <?= e($b['nomor_batch']) ?> — <?= labelSisaHari($b['sisa_hari']) ?></div>
            </div>
            <span class="badge-status badge-kritis">Kritis</span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartStatus'), {
  type: 'doughnut',
  data: {
    labels: ['Aman', 'Perhatian', 'Kritis', 'Kedaluwarsa'],
    datasets: [{
      data: [<?= $countStatus['Aman'] ?>, <?= $countStatus['Perhatian'] ?>, <?= $countStatus['Kritis'] ?>, <?= $countStatus['Kedaluwarsa'] ?>],
      backgroundColor: ['#16a34a', '#d97706', '#ea580c', '#dc2626']
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
