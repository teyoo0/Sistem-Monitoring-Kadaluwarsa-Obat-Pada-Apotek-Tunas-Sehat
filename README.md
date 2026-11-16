# Sistem Informasi Monitoring Kedaluwarsa Obat — Apotek Tunas Sehat

Aplikasi berbasis web (PHP 8.1 & MySQL) untuk memantau status masa simpan batch obat (*Aman*, *Perhatian*, *Kritis*, dan *Kedaluwarsa*) yang dilengkapi dengan **Continuous Integration (CI) Pipeline** menggunakan **GitHub Actions**.

## Anggota Kelompok 7 & Pembagian Peran
1. **Adhysti Putri (F5512510017)** — Inisialisasi repository, pembuatan skrip unit test (`tests/test_fungsi.php`), konfigurasi workflow CI (`.github/workflows/ci.yml`), serta simulasi pengujian *fail-to-pass* melalui Pull Request.
2. **Siti Zahra R. Nulia (F5512510027)** — Validasi logika perhitungan status kedaluwarsa, pengujian *Pull Request*, dan peninjauan kode (*code review*).
3. **Teodorus Dwi Putra Sarungu (F5512510029)** — Penyusunan dokumentasi `README.md`, pencatatan ringkasan error serta langkah perbaikan, dan pengumpulan bukti *run* pipeline.

## Alur Kerja CI Pipeline
Setiap kali terjadi `push` atau `pull_request` ke cabang `main`, GitHub Actions menjalankan workflow `.github/workflows/ci.yml` secara otomatis dengan tahapan:
1. **Checkout Kode Repository** (`actions/checkout@v4`).
2. **Setup Environment PHP 8.1** (`shivammathur/setup-php@v2`).
3. **Verifikasi Lingkungan PHP** (`php -v`).
4. **Build / PHP Linting (`php -l`)** untuk memastikan seluruh berkas `.php` bebas dari kesalahan sintaks.
5. **Unit Testing (`php tests/test_fungsi.php`)** untuk menguji akurasi perhitungan kategori sisa hari kedaluwarsa (*Aman*, *Perhatian*, *Kritis*, dan *Kedaluwarsa*).

## Ringkasan Pengujian *Fail-to-Pass* (Bukti CI)
- **Simulasi Kegagalan (*Fail*):** Penambahan kesalahan sintaks pada berkas `includes/functions.php` di cabang `uji-ci-pipeline` (Pull Request `#1`, commit `621e970`) berhasil ditangkap oleh tahap *PHP Linting* dan menghentikan pipeline dengan status **Failed**.
- **Perbaikan (*Pass*):** Penghapusan baris kesalahan sintaks tersebut pada commit `6c25021` membuat seluruh tahapan *linting* dan *unit test* kembali lulus (**Success**), sehingga Pull Request `#1` dapat di-*merge* ke cabang `main`.

## Cara Menjalankan Pengujian Secara Lokal
Pastikan PHP 8.1 terpasang pada perangkat lokal (misalnya melalui Laragon), lalu jalankan perintah berikut pada terminal di direktori utama proyek:

```bash
# 1. Cek sintaks file PHP
php -l includes/functions.php

# 2. Jalankan unit test logika status kedaluwarsa
php tests/test_fungsi.php