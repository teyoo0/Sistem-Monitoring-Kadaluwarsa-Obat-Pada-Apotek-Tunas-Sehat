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

// Auto-detect BASE_URL (mendukung Laragon subfolder & PHP Built-in Server root)
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$posModules = strpos($scriptName, '/modules/');

if ($posModules !== false) {
    $baseUrl = substr($scriptName, 0, $posModules);
} else {
    $baseUrl = str_replace('\\', '/', dirname($scriptName));
}
define('BASE_URL', rtrim($baseUrl, '/'));

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';