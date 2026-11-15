<?php
// tests/test_fungsi.php
// Pengujian otomatis fungsi perhitungan status kedaluwarsa obat

require_once __DIR__ . '/../includes/functions.php';

echo "=== MENJALANKAN PENGUJIAN UNIT: HITUNG STATUS BATCH ===\n";

$gagal = 0;

function assertStatus(string $label, string $tanggalExp, string $expectedStatus, int &$gagal): void
{
    $hasil = hitungStatusBatch($tanggalExp);
    $aktual = $hasil['status'];
    if ($aktual === $expectedStatus) {
        echo "[PASS] {$label} -> Status: {$aktual} (Sisa: {$hasil['sisa_hari']} hari)\n";
    } else {
        echo "[FAIL] {$label} -> Diharapkan: {$expectedStatus}, Aktual: {$aktual}\n";
        $gagal++;
    }
}

// 1. Uji status Aman (> 90 hari)
assertStatus('Uji Stok Aman (+120 hari)', date('Y-m-d', strtotime('+120 days')), 'Aman', $gagal);

// 2. Uji status Perhatian (31 - 90 hari)
assertStatus('Uji Stok Perhatian (+60 hari)', date('Y-m-d', strtotime('+60 days')), 'Perhatian', $gagal);

// 3. Uji status Kritis (1 - 30 hari)
assertStatus('Uji Stok Kritis (+15 hari)', date('Y-m-d', strtotime('+15 days')), 'Kritis', $gagal);

// 4. Uji status Kedaluwarsa (0 hari atau lewat)
assertStatus('Uji Stok Kedaluwarsa (Hari ini)', date('Y-m-d'), 'Kedaluwarsa', $gagal);
assertStatus('Uji Stok Kedaluwarsa (-10 hari)', date('Y-m-d', strtotime('-10 days')), 'Kedaluwarsa', $gagal);

echo "=======================================================\n";

if ($gagal > 0) {
    echo "HASIL AKHIR: GAGAL ({$gagal} pengujian tidak sesuai)\n";
    exit(1); // Mengirim sinyal error ke CI Pipeline
}

echo "HASIL AKHIR: SUKSES (Semua pengujian lulus!)\n";
exit(0);