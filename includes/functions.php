<?php
// =====================================================
// includes/functions.php
// Satu-satunya sumber logika status kedaluwarsa.
// Dipanggil dari dashboard, batch, monitoring, riwayat
// agar tidak ada duplikasi/ketidakkonsistenan status.
// =====================================================

/**
 * Menghitung sisa hari dan status kedaluwarsa suatu batch.
 * Menggunakan DateTime::diff() sehingga tahun kabisat dan
 * perubahan bulan/tahun ditangani otomatis dengan benar.
 *
 * Aturan:
 *  - Aman       : sisa hari > 90
 *  - Perhatian  : sisa hari 31 - 90
 *  - Kritis     : sisa hari 1 - 30
 *  - Kedaluwarsa: sisa hari <= 0
 */
function hitungStatusBatch(string $tanggal_kedaluwarsa): array
{
    $today = new DateTime(date('Y-m-d'));
    $exp   = new DateTime($tanggal_kedaluwarsa);
    $interval = $today->diff($exp);
    $sisa = (int) $interval->format('%r%a'); // %r = tanda (+/-), %a = jumlah hari absolut

    if ($sisa <= 0) {
        $status = 'Kedaluwarsa';
    } elseif ($sisa <= 30) {
        $status = 'Kritis';
    } elseif ($sisa <= 90) {
        $status = 'Perhatian';
    } else {
        $status = 'Aman';
    }

    return ['sisa_hari' => $sisa, 'status' => $status];
}

/** Mengembalikan class CSS badge sesuai status (konsisten di semua halaman). */
function statusBadgeClass(string $status): string
{
    switch ($status) {
        case 'Aman':        return 'badge-aman';
        case 'Perhatian':   return 'badge-perhatian';
        case 'Kritis':      return 'badge-kritis';
        case 'Kedaluwarsa': return 'badge-kedaluwarsa';
        default:            return 'badge-default';
    }
}

/** Format tanggal ke format Indonesia: 13 September 2026 */
function formatTanggalIndo(?string $tanggal): string
{
    if (empty($tanggal)) return '-';
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $d = new DateTime($tanggal);
    return $d->format('d') . ' ' . $bulan[(int) $d->format('n')] . ' ' . $d->format('Y');
}

/** Shortcut untuk escape output HTML (mencegah XSS). */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Label sisa hari yang ramah dibaca, termasuk kasus sudah lewat. */
function labelSisaHari(int $sisa): string
{
    if ($sisa > 0) return $sisa . ' hari lagi';
    if ($sisa === 0) return 'Hari ini';
    return abs($sisa) . ' hari lalu';
}
