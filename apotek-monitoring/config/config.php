<?php
// =====================================================
// config/config.php
// Bootstrap utama: session, error handling, base url
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Makassar');

// Auto-detect BASE_URL supaya aplikasi tetap jalan baik diakses
// langsung di root (PHP built-in server) maupun di subfolder (XAMPP htdocs).
// Semua file halaman berada 2 folder di bawah root: modules/<nama>/file.php
$scriptName = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptName, '/modules/') !== false) {
    define('BASE_URL', rtrim(dirname($scriptName, 2), '/'))
} else {
    define('BASE_URL', rtrim(dirname($scriptName), '/'));
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
