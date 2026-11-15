<?php
// =====================================================
// config/database.php
// Koneksi database menggunakan PDO (MySQL/MariaDB)
// =====================================================

$DB_HOST = 'localhost';
$DB_NAME = 'apotek_monitoring';
$DB_USER = 'root';
$DB_PASS = ''; // default XAMPP: kosong. Sesuaikan jika password root Anda berbeda.

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL/MariaDB aktif dan database "apotek_monitoring" sudah diimport. Detail: ' . $e->getMessage());
}
