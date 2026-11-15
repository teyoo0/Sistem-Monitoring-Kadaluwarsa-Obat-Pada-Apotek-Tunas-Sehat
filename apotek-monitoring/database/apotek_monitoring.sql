-- =====================================================
-- Database: apotek_monitoring
-- Sistem Informasi Monitoring Kedaluwarsa Obat
-- Apotek Tunas Sehat
-- =====================================================

CREATE DATABASE IF NOT EXISTS apotek_monitoring
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE apotek_monitoring;

-- Hapus tabel lama jika ada (agar file ini aman diimport ulang / reset data)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pemeriksaan;
DROP TABLE IF EXISTS batch;
DROP TABLE IF EXISTS obat;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------
-- Tabel: users
-- ---------------------------------------------------
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- Tabel: obat
-- ---------------------------------------------------
CREATE TABLE obat (
    id_obat INT AUTO_INCREMENT PRIMARY KEY,
    kode_obat VARCHAR(20) NOT NULL UNIQUE,
    nama_obat VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- Tabel: batch
-- ---------------------------------------------------
CREATE TABLE batch (
    id_batch INT AUTO_INCREMENT PRIMARY KEY,
    id_obat INT NOT NULL,
    nomor_batch VARCHAR(50) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    tanggal_kedaluwarsa DATE NOT NULL,
    jumlah_stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_batch_obat FOREIGN KEY (id_obat) REFERENCES obat(id_obat)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- Tabel: pemeriksaan (sekaligus berfungsi sebagai riwayat)
-- ---------------------------------------------------
CREATE TABLE pemeriksaan (
    id_pemeriksaan INT AUTO_INCREMENT PRIMARY KEY,
    id_batch INT NOT NULL,
    id_user INT NOT NULL,
    tanggal_pemeriksaan DATE NOT NULL,
    status_saat_diperiksa VARCHAR(20) NOT NULL,
    hasil_pemeriksaan VARCHAR(150) NOT NULL,
    tindakan VARCHAR(150) NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pemeriksaan_batch FOREIGN KEY (id_batch) REFERENCES batch(id_batch)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pemeriksaan_user FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- SEED DATA (untuk testing/demo)
-- =====================================================

-- User default (password asli: "admin123", di-hash dengan bcrypt)
-- Hash di bawah dibuat menggunakan password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$dGF/P.J/E95usJ1Bns74yewVY36O9YyYd9qtN3Ifb5IR8nY3lZDum', 'Andi Prasetyo'),
('apoteker', '$2y$10$dGF/P.J/E95usJ1Bns74yewVY36O9YyYd9qtN3Ifb5IR8nY3lZDum', 'Siti Rahma');

-- Data Obat
INSERT INTO obat (kode_obat, nama_obat, kategori, satuan, keterangan) VALUES
('OB001', 'Paracetamol 500mg', 'Analgesik', 'Strip', 'Obat penurun panas dan pereda nyeri'),
('OB002', 'Amoxicillin 500mg', 'Antibiotik', 'Strip', 'Antibiotik golongan penisilin'),
('OB003', 'Vitamin C 1000mg', 'Vitamin', 'Botol', 'Suplemen vitamin C'),
('OB004', 'Antasida Doen', 'Obat Lambung', 'Strip', 'Obat maag'),
('OB005', 'Cetirizine 10mg', 'Antihistamin', 'Strip', 'Obat alergi'),
('OB006', 'Ibuprofen 400mg', 'Analgesik', 'Strip', 'Anti nyeri dan anti inflamasi');

-- Data Batch (tanggal disesuaikan relatif terhadap tanggal berjalan agar demo status bervariasi)
-- Aman (> 90 hari), Perhatian (31-90), Kritis (1-30), Kedaluwarsa (<=0)
INSERT INTO batch (id_obat, nomor_batch, tanggal_masuk, tanggal_kedaluwarsa, jumlah_stok) VALUES
(1, 'A001', DATE_SUB(CURDATE(), INTERVAL 200 DAY), DATE_ADD(CURDATE(), INTERVAL 150 DAY), 500),
(1, 'A002', DATE_SUB(CURDATE(), INTERVAL 300 DAY), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 120),
(2, 'B001', DATE_SUB(CURDATE(), INTERVAL 100 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 300),
(2, 'B002', DATE_SUB(CURDATE(), INTERVAL 400 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), 80),
(3, 'C001', DATE_SUB(CURDATE(), INTERVAL 50 DAY), DATE_ADD(CURDATE(), INTERVAL 200 DAY), 60),
(4, 'D001', DATE_SUB(CURDATE(), INTERVAL 120 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 90),
(5, 'E001', DATE_SUB(CURDATE(), INTERVAL 80 DAY), DATE_ADD(CURDATE(), INTERVAL 45 DAY), 150),
(6, 'F001', DATE_SUB(CURDATE(), INTERVAL 250 DAY), DATE_SUB(CURDATE(), INTERVAL 20 DAY), 40),
(3, 'C002', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 400 DAY), 100);

-- Data Pemeriksaan contoh (riwayat)
INSERT INTO pemeriksaan (id_batch, id_user, tanggal_pemeriksaan, status_saat_diperiksa, hasil_pemeriksaan, tindakan, catatan) VALUES
(4, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Kedaluwarsa', 'Obat ditemukan secara fisik', 'Dipisahkan dari stok', 'Menunggu proses pemusnahan'),
(8, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Kedaluwarsa', 'Obat ditemukan secara fisik', 'Dipisahkan dari stok', 'Dilaporkan ke apoteker penanggung jawab');
