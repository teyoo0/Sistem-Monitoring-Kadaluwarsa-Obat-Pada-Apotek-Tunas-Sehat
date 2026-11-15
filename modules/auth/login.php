<?php
require_once __DIR__ . '/../../config/config.php';

if (!empty($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['id_user']      = $user['id_user'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

$expired = isset($_GET['expired']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Apotek Tunas Sehat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <span class="login-icon">&#9877;</span>
    <h2>Apotek Tunas Sehat</h2>
    <p class="subtitle">Sistem Monitoring Kedaluwarsa Obat</p>

    <?php if ($expired): ?>
      <div class="alert alert-warning py-2 small">Sesi Anda telah berakhir. Silakan login kembali.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary-app w-100">Masuk</button>
    </form>

    <div class="text-center mt-3" style="font-size:12px;color:#9ca3af;">
      Akun demo: <strong>admin</strong> / <strong>admin123</strong>
    </div>
  </div>
</div>
</body>
</html>
