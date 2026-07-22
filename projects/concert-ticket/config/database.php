<?php
// ==========================================
// KONFIGURASI DATABASE
// ==========================================

// Pilih Driver: 'pgsql' untuk Supabase (PostgreSQL), atau 'mysql' untuk MySQL lokal (XAMPP)
$db_driver = 'pgsql'; 

// --- Konfigurasi Supabase (PostgreSQL) ---
$pg_host = 'aws-0-ap-northeast-1.pooler.supabase.com'; // Host Pooler Tokyo
$pg_port = '6543';                                     // Port Pooler
$pg_db   = 'postgres';                                 // Nama database bawaan Supabase
$pg_user = 'postgres.ymhpqpaviotashqyzvzp';            // Username Pooler format: [user].[ref_id]
$pg_pass = 'RALGumFfer73TfXQ';                   // Masukkan Password database Supabase Anda

// --- Konfigurasi MySQL Lokal (XAMPP) ---
$my_host = 'localhost';
$my_db   = 'db_aidfest';
$my_user = 'root';
$my_pass = ''; // Default kosong di XAMPP
$my_charset = 'utf8mb4';

// ==========================================
// INISIALISASI KONEKSI PDO & MIGRASI
// ==========================================
try {
    if ($db_driver === 'pgsql') {
        // DSN untuk PostgreSQL (dengan sslmode=require untuk keamanan koneksi Supabase)
        $dsn = "pgsql:host=$pg_host;port=$pg_port;dbname=$pg_db;sslmode=require";
        $pdo = new PDO($dsn, $pg_user, $pg_pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // 1. Membuat/Menyesuaikan Tabel Pendaftaran Tiket di PostgreSQL
        $tableExists = $pdo->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = 'pendaftaran_tiket'
        )")->fetchColumn();

        if (!$tableExists) {
            $sql = "CREATE TABLE pendaftaran_tiket (
                id_tiket SERIAL PRIMARY KEY,
                nama_pemesan VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                kategori_tiket VARCHAR(20) NOT NULL CHECK (kategori_tiket IN ('Reguler', 'VIP', 'VVIP')),
                paket_hari VARCHAR(20) DEFAULT '2-Day Pass' CHECK (paket_hari IN ('Day 1', 'Day 2', '2-Day Pass')),
                status_pembayaran VARCHAR(20) DEFAULT 'Pending' CHECK (status_pembayaran IN ('Pending', 'Lunas', 'Batal')),
                metode_pembayaran VARCHAR(50) DEFAULT NULL,
                kode_pembayaran VARCHAR(50) DEFAULT NULL,
                tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
            $pdo->exec($sql);

            // Menambahkan data awal (seeding)
            $stmt = $pdo->prepare("INSERT INTO pendaftaran_tiket (nama_pemesan, email, kategori_tiket, paket_hari, status_pembayaran, metode_pembayaran, kode_pembayaran, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['Rian Hidayat', 'rian.hidayat@example.com', 'VIP', '2-Day Pass', 'Lunas', 'Transfer Bank BCA', 'TRX-78321', date('Y-m-d H:i:s', strtotime('-2 days'))]);
            $stmt->execute(['Amelia Putri', 'amelia.putri@example.com', 'VVIP', '2-Day Pass', 'Pending', null, null, date('Y-m-d H:i:s', strtotime('-1 day'))]);
            $stmt->execute(['Budi Santoso', 'budi.santoso@example.com', 'Reguler', 'Day 1', 'Lunas', 'GoPay', 'TRX-90124', date('Y-m-d H:i:s')]);
        } else {
            // Migrasi kolom jika tabel sudah ada dari langkah sebelumnya
            $columns = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'pendaftaran_tiket'")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('paket_hari', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN paket_hari VARCHAR(20) DEFAULT '2-Day Pass' CHECK (paket_hari IN ('Day 1', 'Day 2', '2-Day Pass'))");
            }
            if (!in_array('status_pembayaran', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN status_pembayaran VARCHAR(20) DEFAULT 'Pending' CHECK (status_pembayaran IN ('Pending', 'Lunas', 'Batal'))");
            }
            if (!in_array('metode_pembayaran', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN metode_pembayaran VARCHAR(50) DEFAULT NULL");
            }
            if (!in_array('kode_pembayaran', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN kode_pembayaran VARCHAR(50) DEFAULT NULL");
            }
        }

        // 2. Membuat Tabel Admin jika belum ada
        $adminTableExists = $pdo->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = 'admin'
        )")->fetchColumn();

        if (!$adminTableExists) {
            $sqlAdmin = "CREATE TABLE admin (
                id_admin SERIAL PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL
            );";
            $pdo->exec($sqlAdmin);
        }

        // Pengaman: Jika tabel admin kosong, tambahkan admin bawaan
        $adminCount = $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
        if ($adminCount == 0) {
            $username = 'admin';
            $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
            $stmtAdmin = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            $stmtAdmin->execute([$username, $hashedPassword]);
        }

    } else {
        // DSN untuk MySQL
        $dsn = "mysql:host=$my_host;charset=$my_charset";
        $pdo = new PDO($dsn, $my_user, $my_pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        // Membuat database jika belum ada
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$my_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$my_db`");

        // 1. Membuat/Menyesuaikan Tabel Pendaftaran Tiket
        $tableExists = $pdo->query("SHOW TABLES LIKE 'pendaftaran_tiket'")->rowCount() > 0;
        if (!$tableExists) {
            $sql = "CREATE TABLE pendaftaran_tiket (
                id_tiket INT AUTO_INCREMENT PRIMARY KEY,
                nama_pemesan VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                kategori_tiket ENUM('Reguler', 'VIP', 'VVIP') NOT NULL,
                paket_hari ENUM('Day 1', 'Day 2', '2-Day Pass') DEFAULT '2-Day Pass',
                status_pembayaran ENUM('Pending', 'Lunas', 'Batal') DEFAULT 'Pending',
                metode_pembayaran VARCHAR(50) DEFAULT NULL,
                kode_pembayaran VARCHAR(50) DEFAULT NULL,
                tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $pdo->exec($sql);

            // Menambahkan data awal (seeding)
            $stmt = $pdo->prepare("INSERT INTO pendaftaran_tiket (nama_pemesan, email, kategori_tiket, paket_hari, status_pembayaran, metode_pembayaran, kode_pembayaran, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['Rian Hidayat', 'rian.hidayat@example.com', 'VIP', '2-Day Pass', 'Lunas', 'Transfer Bank BCA', 'TRX-78321', date('Y-m-d H:i:s', strtotime('-2 days'))]);
            $stmt->execute(['Amelia Putri', 'amelia.putri@example.com', 'VVIP', '2-Day Pass', 'Pending', null, null, date('Y-m-d H:i:s', strtotime('-1 day'))]);
            $stmt->execute(['Budi Santoso', 'budi.santoso@example.com', 'Reguler', 'Day 1', 'Lunas', 'GoPay', 'TRX-90124', date('Y-m-d H:i:s')]);
        } else {
            // Migrasi kolom jika tabel sudah ada dari langkah sebelumnya
            $columns = $pdo->query("DESCRIBE pendaftaran_tiket")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('paket_hari', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN paket_hari ENUM('Day 1', 'Day 2', '2-Day Pass') DEFAULT '2-Day Pass' AFTER kategori_tiket");
            }
            if (!in_array('status_pembayaran', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN status_pembayaran ENUM('Pending', 'Lunas', 'Batal') DEFAULT 'Pending' AFTER kategori_tiket");
            }
            if (!in_array('metode_pembayaran', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN metode_pembayaran VARCHAR(50) DEFAULT NULL AFTER status_pembayaran");
            }
            if (!in_array('kode_pembayaran', $columns)) {
                $pdo->exec("ALTER TABLE pendaftaran_tiket ADD COLUMN kode_pembayaran VARCHAR(50) DEFAULT NULL AFTER metode_pembayaran");
            }
        }

        // 2. Membuat Tabel Admin jika belum ada
        $adminTableExists = $pdo->query("SHOW TABLES LIKE 'admin'")->rowCount() > 0;
        if (!$adminTableExists) {
            $sqlAdmin = "CREATE TABLE admin (
                id_admin INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";
            $pdo->exec($sqlAdmin);
        }

        // Pengaman: Jika tabel admin kosong, tambahkan admin bawaan
        $adminCount = $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
        if ($adminCount == 0) {
            $username = 'admin';
            $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
            $stmtAdmin = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            $stmtAdmin->execute([$username, $hashedPassword]);
        }
    }
} catch (PDOException $e) {
    die("Kesalahan Koneksi Database: " . $e->getMessage());
}
