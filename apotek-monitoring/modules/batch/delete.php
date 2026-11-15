<?php
require_once __DIR__ . '/../../config/config.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);

try {
    $stmt = $pdo->prepare("DELETE FROM batch WHERE id_batch = ?");
    $stmt->execute([$id]);
    header('Location: index.php?success=' . urlencode('Data batch berhasil dihapus.'));
} catch (PDOException $e) {
    header('Location: index.php?error=' . urlencode('Gagal menghapus data batch.'));
}
exit;
