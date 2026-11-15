<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);

$cekBatch = $pdo->prepare("SELECT COUNT(*) c FROM batch WHERE id_obat = ?");
$cekBatch->execute([$id]);

if ($cekBatch->fetch()['c'] > 0) {
    header('Location: index.php?error=' . urlencode('Obat tidak dapat dihapus karena masih memiliki data batch. Hapus batch terkait terlebih dahulu.'));
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM obat WHERE id_obat = ?");
    $stmt->execute([$id]);
    header('Location: index.php?success=' . urlencode('Data obat berhasil dihapus.'));
} catch (PDOException $e) {
    header('Location: index.php?error=' . urlencode('Gagal menghapus data obat.'));
}
exit;
