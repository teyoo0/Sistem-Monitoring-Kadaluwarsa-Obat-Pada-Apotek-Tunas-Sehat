<?php
// includes/header.php
// Variabel $pageTitle harus di-set di halaman pemanggil sebelum include ini.
$pageTitle = $pageTitle ?? 'Sistem Monitoring';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — Apotek Tunas Sehat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="app-main">
    <header class="app-topbar">
      <button class="btn-sidebar-toggle d-lg-none" id="sidebarToggle" type="button" aria-label="Buka menu">&#9776;</button>
      <h1 class="page-title"><?= e($pageTitle) ?></h1>
      <div class="topbar-user">
        <span class="user-name"><?= e($user['nama_lengkap']) ?></span>
        <a href="<?= BASE_URL ?>/modules/auth/logout.php" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Yakin ingin logout?');">Logout</a>
      </div>
    </header>
    <main class="app-content">
