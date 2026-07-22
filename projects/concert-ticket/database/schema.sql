CREATE DATABASE IF NOT EXISTS db_aidfest;
USE db_aidfest;

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Pendaftaran Tiket
CREATE TABLE IF NOT EXISTS pendaftaran_tiket (
    id_tiket INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    kategori_tiket ENUM('Reguler', 'VIP', 'VVIP') NOT NULL,
    paket_hari ENUM('Day 1', 'Day 2', '2-Day Pass') DEFAULT '2-Day Pass',
    status_pembayaran ENUM('Pending', 'Lunas', 'Batal') DEFAULT 'Pending',
    metode_pembayaran VARCHAR(50) DEFAULT NULL,
    kode_pembayaran VARCHAR(50) DEFAULT NULL,
    tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seeding akun admin default (Username: admin, Password: password123)
-- Catatan: Password tersimpan menggunakan Hash (password_hash)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$w8.1UuC.7J.e0WnO5PzFDeqK76K63K1gZz0rA.Zg3Jb2fLz8z4g2a') -- Password: password123
ON DUPLICATE KEY UPDATE username=username;

-- Seeding data awal pemesan tiket
INSERT INTO pendaftaran_tiket (nama_pemesan, email, kategori_tiket, paket_hari, status_pembayaran, metode_pembayaran, kode_pembayaran, tanggal_pesan) VALUES
('Rian Hidayat', 'rian.hidayat@example.com', 'VIP', '2-Day Pass', 'Lunas', 'Transfer Bank BCA', 'TRX-78321', NOW() - INTERVAL 2 DAY),
('Amelia Putri', 'amelia.putri@example.com', 'VVIP', '2-Day Pass', 'Pending', NULL, NULL, NOW() - INTERVAL 1 DAY),
('Budi Santoso', 'budi.santoso@example.com', 'Reguler', 'Day 1', 'Lunas', 'GoPay', 'TRX-90124', NOW())
ON DUPLICATE KEY UPDATE email=email;
