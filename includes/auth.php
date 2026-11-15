<?php
// =====================================================
// includes/auth.php
// Proteksi halaman berbasis session
// =====================================================

/** Wajib dipanggil di awal setiap halaman yang butuh login. */
function require_login(): void
{
    if (empty($_SESSION['id_user'])) {
        header('Location: ' . BASE_URL . '/modules/auth/login.php?expired=1');
        exit;
    }
}

/** Data pengguna yang sedang login (dari session, bukan query ulang setiap saat). */
function current_user(): array
{
    return [
        'id_user'      => $_SESSION['id_user'] ?? null,
        'username'     => $_SESSION['username'] ?? '',
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? '',
    ];
}
