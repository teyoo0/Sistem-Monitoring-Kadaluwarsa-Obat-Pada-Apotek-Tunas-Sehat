<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$pageTitle = 'Catat Pemeriksaan';
$idBatch = (int) ($_GET['id_batch'] ?? $_POST['id_batch'] ?? 0);

$stmt = $pdo->prepare("SELECT b.*, o.nama_obat, o.kode_obat FROM batch b JOIN obat o ON o.id_obat=b.id_obat WHERE b.id_batch = ?");
$stmt->execute([$idBatch]);
$batch = $stmt->fetch();

if (!$batch) {
    header('Location: ../monitoring/index.php?error=' . urlencode('Batch tidak ditemukan. Silakan pilih batch dari halaman Monitoring.'));
    exit;
}

$statusInfo = hitungStatusBatch($batch['tanggal_kedaluwarsa']);
$errors = [];
$old = ['tanggal_pemeriksaan' => date('Y-m-d'), 'hasil_pemeriksaan' => '', 'tindakan' => '', 'catatan' => ''];

$hasilOptions = ['Obat ditemukan secara fisik', 'Obat tidak ditemukan secara fisik', 'Kondisi fisik obat rusak'];
$tindakanOptions = ['Dipisahkan dari stok', 'Dikembalikan ke stok utama', 'Menunggu proses pemusnahan', 'Tidak ada tindakan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'tanggal_pemeriksaan' => trim($_POST['tanggal_pemeriksaan'] ?? ''),
        'hasil_pemeriksaan'   => trim($_POST['hasil_pemeriksaan'] ?? ''),
        'tindakan'            => trim($_POST['tindakan'] ?? ''),
        'catatan'             => trim($_POST['catatan'] ?? ''),
    ];

    if ($old['tanggal_pemeriksaan'] === '') $errors[] = 'Tanggal pemeriksaan wajib diisi.';
    if ($old['hasil_pemeriksaan'] === '') $errors[] = 'Hasil pemeriksaan wajib dipilih.';
    if ($old['tindakan'] === '') $errors[] = 'Tindakan wajib dipilih.';

    if (empty($errors)) {
        $user = current_user();
        $stmt = $pdo->prepare("INSERT INTO pemeriksaan (id_batch, id_user, tanggal_pemeriksaan, status_saat_diperiksa, hasil_pemeriksaan, tindakan, catatan) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $idBatch,
            $user['id_user'],
            $old['tanggal_pemeriksaan'],
            $statusInfo['status'],
            $old['hasil_pemeriksaan'],
            $old['tindakan'],
            $old['catatan'] ?: null,
        ]);
        header('Location: index.php?success=' . urlencode('Data pemeriksaan berhasil disimpan.'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="panel" style="max-width:640px;">
  <div class="mb-3 p-3" style="background:#f9fafb;border-radius:8px;">
    <div class="fw-semibold"><?= e($batch['nama_obat']) ?> (<?= e($batch['kode_obat']) ?>)</div>
    <div class="small text-muted">Batch <?= e($batch['nomor_batch']) ?> — Kedaluwarsa <?= formatTanggalIndo($batch['tanggal_kedaluwarsa']) ?></div>
    <div class="mt-2"><span class="badge-status <?= statusBadgeClass($statusInfo['status']) ?>"><?= $statusInfo['status'] ?></span>
      <span class="small text-muted ms-1"><?= labelSisaHari($statusInfo['sisa_hari']) ?></span>
    </div>
  </div>

  <?php foreach ($errors as $err): ?>
    <div class="alert alert-danger py-2"><?= e($err) ?></div>
  <?php endforeach; ?>

  <form method="POST" novalidate>
    <input type="hidden" name="id_batch" value="<?= $idBatch ?>">
    <div class="mb-3">
      <label class="form-label">Tanggal Pemeriksaan <span class="required-mark">*</span></label>
      <input type="date" name="tanggal_pemeriksaan" class="form-control" value="<?= e($old['tanggal_pemeriksaan']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Hasil Pemeriksaan <span class="required-mark">*</span></label>
      <select name="hasil_pemeriksaan" class="form-select" required>
        <option value="">-- Pilih Hasil --</option>
        <?php foreach ($hasilOptions as $h): ?>
          <option value="<?= e($h) ?>" <?= $old['hasil_pemeriksaan'] === $h ? 'selected' : '' ?>><?= e($h) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Tindakan <span class="required-mark">*</span></label>
      <select name="tindakan" class="form-select" required>
        <option value="">-- Pilih Tindakan --</option>
        <?php foreach ($tindakanOptions as $t): ?>
          <option value="<?= e($t) ?>" <?= $old['tindakan'] === $t ? 'selected' : '' ?>><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Catatan</label>
      <textarea name="catatan" class="form-control" rows="3"><?= e($old['catatan']) ?></textarea>
    </div>
    <div class="alert alert-info small py-2">Pemeriksaan ini hanya mencatat hasil pengecekan fisik yang Anda lakukan. Sistem tidak melakukan pemeriksaan fisik secara otomatis.</div>
    <button type="submit" class="btn btn-primary-app text-white">Simpan Pemeriksaan</button>
    <a href="../monitoring/index.php" class="btn btn-outline-secondary">Batal</a>
  </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
