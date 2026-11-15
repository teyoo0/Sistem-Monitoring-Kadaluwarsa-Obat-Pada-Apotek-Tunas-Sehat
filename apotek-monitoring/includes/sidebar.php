<?php
$currentPath = $_SERVER['SCRIPT_NAME'];
function navActive($needle, $currentPath) {
    return strpos($currentPath, $needle) !== false ? 'active' : '';
}
?>
<aside class="app-sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <span class="brand-icon">&#9877;</span>
    <div>
      <div class="brand-title">Tunas Sehat</div>
      <div class="brand-sub">Monitoring Obat</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a href="<?= BASE_URL ?>/modules/dashboard/index.php" class="<?= navActive('/dashboard/', $currentPath) ?>">
      <span class="nav-icon">&#9635;</span> Dashboard
    </a>
    <a href="<?= BASE_URL ?>/modules/obat/index.php" class="<?= navActive('/obat/', $currentPath) ?>">
      <span class="nav-icon">&#128138;</span> Data Obat
    </a>
    <a href="<?= BASE_URL ?>/modules/batch/index.php" class="<?= navActive('/batch/', $currentPath) ?>">
      <span class="nav-icon">&#128230;</span> Data Batch
    </a>
    <a href="<?= BASE_URL ?>/modules/monitoring/index.php" class="<?= navActive('/monitoring/', $currentPath) ?>">
      <span class="nav-icon">&#128269;</span> Monitoring
    </a>
    <a href="<?= BASE_URL ?>/modules/pemeriksaan/index.php" class="<?= navActive('/pemeriksaan/', $currentPath) ?>">
      <span class="nav-icon">&#9989;</span> Pemeriksaan/Tindakan
    </a>
    <a href="<?= BASE_URL ?>/modules/riwayat/index.php" class="<?= navActive('/riwayat/', $currentPath) ?>">
      <span class="nav-icon">&#128220;</span> Riwayat
    </a>
    <a href="<?= BASE_URL ?>/modules/statistik/index.php" class="<?= navActive('/statistik/', $currentPath) ?>">
      <span class="nav-icon">&#128202;</span> Statistik/Analisis
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= BASE_URL ?>/modules/auth/logout.php" onclick="return confirm('Yakin ingin logout?');">
      <span class="nav-icon">&#9099;</span> Logout
    </a>
  </div>
</aside>
