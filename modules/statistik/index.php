<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Statistik & Analisis';

// 1. Jumlah batch berdasarkan status (dihitung real-time, bukan dari kolom statis)
$allBatch = $pdo->query("SELECT tanggal_kedaluwarsa FROM batch")->fetchAll();
$countStatus = ['Aman' => 0, 'Perhatian' => 0, 'Kritis' => 0, 'Kedaluwarsa' => 0];
foreach ($allBatch as $b) {
    $countStatus[hitungStatusBatch($b['tanggal_kedaluwarsa'])['status']]++;
}

// 2. Jumlah batch berdasarkan kategori obat
$stmt = $pdo->query("
    SELECT o.kategori, COUNT(b.id_batch) AS jumlah
    FROM obat o LEFT JOIN batch b ON b.id_obat = o.id_obat
    GROUP BY o.kategori ORDER BY jumlah DESC
");
$perKategori = $stmt->fetchAll();

// 3. Obat yang paling sering memiliki batch berstatus Kedaluwarsa (dari histori pemeriksaan)
$stmt = $pdo->query("
    SELECT o.nama_obat, COUNT(*) AS jumlah
    FROM pemeriksaan p
    JOIN batch b ON b.id_batch = p.id_batch
    JOIN obat o ON o.id_obat = b.id_obat
    WHERE p.status_saat_diperiksa = 'Kedaluwarsa'
    GROUP BY o.nama_obat
    ORDER BY jumlah DESC
    LIMIT 5
");
$obatSeringExpired = $stmt->fetchAll();

// 4. Jumlah batch kedaluwarsa per bulan (berdasarkan tanggal kedaluwarsa aktual, 6 bulan terakhir s.d. akan datang)
$stmt = $pdo->query("
    SELECT DATE_FORMAT(tanggal_kedaluwarsa, '%Y-%m') AS bulan, COUNT(*) AS jumlah
    FROM batch
    WHERE tanggal_kedaluwarsa <= CURDATE()
    GROUP BY bulan ORDER BY bulan ASC
");
$perBulan = $stmt->fetchAll();

// 5. Jumlah pemeriksaan berdasarkan tindakan
$stmt = $pdo->query("SELECT tindakan, COUNT(*) AS jumlah FROM pemeriksaan GROUP BY tindakan ORDER BY jumlah DESC");
$perTindakan = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-title">Jumlah Batch Berdasarkan Status</div>
      <canvas id="chartStatus" height="200"></canvas>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-title">Jumlah Batch Berdasarkan Kategori Obat</div>
      <?php if (empty($perKategori)): ?>
        <div class="empty-state">Belum ada data.</div>
      <?php else: ?>
      <canvas id="chartKategori" height="200"></canvas>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-title">Obat Paling Sering Berstatus Kedaluwarsa (histori pemeriksaan)</div>
      <?php if (empty($obatSeringExpired)): ?>
        <div class="empty-state"><div class="icon">&#128077;</div>Belum ada riwayat pemeriksaan berstatus kedaluwarsa.</div>
      <?php else: ?>
      <table class="table-app">
        <thead><tr><th>Nama Obat</th><th>Jumlah Kejadian</th></tr></thead>
        <tbody>
        <?php foreach ($obatSeringExpired as $o): ?>
          <tr><td><?= e($o['nama_obat']) ?></td><td><?= $o['jumlah'] ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="panel">
      <div class="panel-title">Jumlah Pemeriksaan Berdasarkan Tindakan</div>
      <?php if (empty($perTindakan)): ?>
        <div class="empty-state">Belum ada data pemeriksaan.</div>
      <?php else: ?>
      <table class="table-app">
        <thead><tr><th>Tindakan</th><th>Jumlah</th></tr></thead>
        <tbody>
        <?php foreach ($perTindakan as $t): ?>
          <tr><td><?= e($t['tindakan']) ?></td><td><?= $t['jumlah'] ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-12">
    <div class="panel">
      <div class="panel-title">Tren Batch Kedaluwarsa per Bulan</div>
      <?php if (empty($perBulan)): ?>
        <div class="empty-state"><div class="icon">&#128200;</div>Belum ada batch yang kedaluwarsa untuk ditampilkan trennya.</div>
      <?php else: ?>
      <canvas id="chartTren" height="180"></canvas>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartStatus'), {
  type: 'bar',
  data: {
    labels: ['Aman', 'Perhatian', 'Kritis', 'Kedaluwarsa'],
    datasets: [{
      label: 'Jumlah Batch',
      data: [<?= $countStatus['Aman'] ?>, <?= $countStatus['Perhatian'] ?>, <?= $countStatus['Kritis'] ?>, <?= $countStatus['Kedaluwarsa'] ?>],
      backgroundColor: ['#16a34a', '#d97706', '#ea580c', '#dc2626']
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

<?php if (!empty($perKategori)): ?>
new Chart(document.getElementById('chartKategori'), {
  type: 'pie',
  data: {
    labels: <?= json_encode(array_column($perKategori, 'kategori')) ?>,
    datasets: [{ data: <?= json_encode(array_map('intval', array_column($perKategori, 'jumlah'))) ?>,
      backgroundColor: ['#0f766e','#0891b2','#7c3aed','#db2777','#f59e0b','#65a30d','#4338ca'] }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});
<?php endif; ?>

<?php if (!empty($perBulan)): ?>
new Chart(document.getElementById('chartTren'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($perBulan, 'bulan')) ?>,
    datasets: [{
      label: 'Batch Kedaluwarsa',
      data: <?= json_encode(array_map('intval', array_column($perBulan, 'jumlah'))) ?>,
      borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.1)', fill: true, tension: 0.3
    }]
  },
  options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
