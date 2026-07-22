<?php
// includes/functions.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Memuat konfigurasi database
require_once __DIR__ . '/../config/database.php';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

/**
 * Memeriksa apakah admin sudah login
 * @return bool
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Membatasi akses halaman hanya untuk admin (redirect ke login jika belum login)
 */
function require_admin_login() {
    if (!is_admin_logged_in()) {
        $_SESSION['error'] = "Anda harus login terlebih dahulu untuk mengakses halaman ini.";
        header("Location: login.php");
        exit();
    }
}

/**
 * Melakukan proses login admin
 * @param PDO $pdo
 * @param string $username
 * @param string $password
 * @return bool
 */
function login_admin($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Mendapatkan semua data pendaftaran tiket (dengan pencarian opsional)
 * @param PDO $pdo
 * @param string $search
 * @return array
 */
function get_all_tickets($pdo, $search = '') {
    try {
        if ($search !== '') {
            $sql = "SELECT * FROM pendaftaran_tiket WHERE nama_pemesan LIKE :search OR email LIKE :search ORDER BY tanggal_pesan DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $sql = "SELECT * FROM pendaftaran_tiket ORDER BY tanggal_pesan DESC";
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Gagal memuat data pendaftaran: " . $e->getMessage());
    }
}

/**
 * Mendapatkan data tiket berdasarkan ID
 * @param PDO $pdo
 * @param int $id
 * @return array|false
 */
function get_ticket_by_id($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pendaftaran_tiket WHERE id_tiket = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        die("Gagal mengambil data tiket: " . $e->getMessage());
    }
}

/**
 * Mendapatkan data tiket berdasarkan Email
 * @param PDO $pdo
 * @param string $email
 * @return array|false
 */
function get_ticket_by_email($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pendaftaran_tiket WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        die("Gagal mencari tiket: " . $e->getMessage());
    }
}

/**
 * Mendaftarkan tiket baru ke database
 * @param PDO $pdo
 * @param string $nama
 * @param string $email
 * @param string $kategori
 * @param string $paket_hari
 * @param string $status_pembayaran
 * @param string|null $metode_pembayaran
 * @param string|null $kode_pembayaran
 * @return int|bool Mengembalikan ID tiket yang baru saja dibuat jika berhasil, atau false
 */
function create_ticket($pdo, $nama, $email, $kategori, $paket_hari = '2-Day Pass', $status_pembayaran = 'Pending', $metode_pembayaran = null, $kode_pembayaran = null) {
    try {
        $sql = "INSERT INTO pendaftaran_tiket (nama_pemesan, email, kategori_tiket, paket_hari, status_pembayaran, metode_pembayaran, kode_pembayaran, tanggal_pesan) 
                VALUES (:nama, :email, :kategori, :hari, :status, :metode, :kode, NOW())";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            'nama' => $nama,
            'email' => $email,
            'kategori' => $kategori,
            'hari' => $paket_hari,
            'status' => $status_pembayaran,
            'metode' => $metode_pembayaran,
            'kode' => $kode_pembayaran
        ]);
        if ($success) {
            if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') {
                return $pdo->lastInsertId('pendaftaran_tiket_id_tiket_seq');
            }
            return $pdo->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Memperbarui data tiket yang sudah ada
 * @param PDO $pdo
 * @param int $id
 * @param string $nama
 * @param string $email
 * @param string $kategori
 * @param string $paket_hari
 * @param string $status_pembayaran
 * @param string|null $metode_pembayaran
 * @param string|null $kode_pembayaran
 * @return bool
 */
function update_ticket($pdo, $id, $nama, $email, $kategori, $paket_hari = '2-Day Pass', $status_pembayaran = 'Pending', $metode_pembayaran = null, $kode_pembayaran = null) {
    try {
        $sql = "UPDATE pendaftaran_tiket SET 
                nama_pemesan = :nama, 
                email = :email, 
                kategori_tiket = :kategori, 
                paket_hari = :hari,
                status_pembayaran = :status, 
                metode_pembayaran = :metode, 
                kode_pembayaran = :kode 
                WHERE id_tiket = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'nama' => $nama,
            'email' => $email,
            'kategori' => $kategori,
            'hari' => $paket_hari,
            'status' => $status_pembayaran,
            'metode' => $metode_pembayaran,
            'kode' => $kode_pembayaran,
            'id' => $id
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Memperbarui status pembayaran tiket (digunakan saat checkout/proses bayar)
 * @param PDO $pdo
 * @param int $id
 * @param string $status
 * @param string $metode
 * @param string $kode
 * @return bool
 */
function update_payment_status($pdo, $id, $status, $metode, $kode) {
    try {
        $sql = "UPDATE pendaftaran_tiket SET 
                status_pembayaran = :status, 
                metode_pembayaran = :metode, 
                kode_pembayaran = :kode 
                WHERE id_tiket = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'metode' => $metode,
            'kode' => $kode,
            'id' => $id
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Menghapus data tiket dari database
 * @param PDO $pdo
 * @param int $id
 * @return bool
 */
function delete_ticket($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM pendaftaran_tiket WHERE id_tiket = :id");
        return $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Memeriksa apakah email sudah terdaftar di database
 * @param PDO $pdo
 * @param string $email
 * @param int $exclude_id (opsional, untuk case update)
 * @return bool
 */
function is_email_registered($pdo, $email, $exclude_id = 0) {
    try {
        if ($exclude_id > 0) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran_tiket WHERE email = :email AND id_tiket != :id");
            $stmt->execute(['email' => $email, 'id' => $exclude_id]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran_tiket WHERE email = :email");
            $stmt->execute(['email' => $email]);
        }
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        die("Gagal memvalidasi keunikan email: " . $e->getMessage());
    }
}

/**
 * Mendapatkan tarif harga tiket berdasarkan kategori dan paket hari
 * @param string $kategori
 * @param string $paket_hari
 * @return int
 */
function get_ticket_price($kategori, $paket_hari) {
    if ($paket_hari === '2-Day Pass') {
        switch ($kategori) {
            case 'Reguler': return 14000000;
            case 'VIP': return 32500000;
            case 'VVIP': return 85000000;
        }
    } else {
        switch ($kategori) {
            case 'Reguler': return 7500000;
            case 'VIP': return 16500000;
        }
    }
    return 0;
}

/**
 * Mengambil data statistik jumlah tiket dan pendapatan
 * @param PDO $pdo
 * @return array
 */
function get_ticket_statistics($pdo) {
    try {
        $stats = [
            'total' => $pdo->query("SELECT COUNT(*) FROM pendaftaran_tiket")->fetchColumn(),
            'reguler' => $pdo->query("SELECT COUNT(*) FROM pendaftaran_tiket WHERE kategori_tiket = 'Reguler'")->fetchColumn(),
            'vip' => $pdo->query("SELECT COUNT(*) FROM pendaftaran_tiket WHERE kategori_tiket = 'VIP'")->fetchColumn(),
            'vvip' => $pdo->query("SELECT COUNT(*) FROM pendaftaran_tiket WHERE kategori_tiket = 'VVIP'")->fetchColumn(),
            'pendapatan' => 0
        ];

        $stmt = $pdo->query("SELECT kategori_tiket, paket_hari FROM pendaftaran_tiket WHERE status_pembayaran = 'Lunas'");
        $tickets = $stmt->fetchAll();
        foreach ($tickets as $t) {
            $stats['pendapatan'] += get_ticket_price($t['kategori_tiket'], $t['paket_hari']);
        }

        return $stats;
    } catch (PDOException $e) {
        die("Gagal memuat statistik dashboard: " . $e->getMessage());
    }
}

/**
 * Memformat string tanggal database menjadi format bahasa Indonesia
 * @param string $dateStr
 * @return string
 */
function format_tanggal_indo($dateStr) {
    $timestamp = strtotime($dateStr);
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $numHari = date('w', $timestamp);
    $tgl = date('j', $timestamp);
    $numBulan = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    
    return $hari[$numHari] . ", " . $tgl . " " . $bulan[$numBulan] . " " . $tahun . " - " . $jam . " WIB";
}
/**
 * Mengirimkan email konfirmasi E-Ticket kepada pembeli
 * @param string $to_email
 * @param string $nama
 * @param array $ticket
 * @return bool
 */
function send_ticket_email($to_email, $nama, $ticket) {
    $mail_config_file = __DIR__ . '/../config/mail.php';
    if (!file_exists($mail_config_file)) {
        return false;
    }
    
    $config = require $mail_config_file;
    
    if (!$config['mail_enabled']) {
        $log_dir = __DIR__ . '/../scratch/';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        $log_content = "[" . date('Y-m-d H:i:s') . "] SIMULASI EMAIL DIKIRIM KE: $to_email\n";
        $log_content .= "Nama: $nama\nTiket ID: #ADF-" . str_pad($ticket['id_tiket'], 5, '0', STR_PAD_LEFT) . "\nKategori: " . $ticket['kategori_tiket'] . "\nHari: " . $ticket['paket_hari'] . "\nKode Bayar: " . $ticket['kode_pembayaran'] . "\n=========================================\n";
        file_put_contents($log_dir . 'mail_sim_log.txt', $log_content, FILE_APPEND);
        return true;
    }
    
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_user'];
        $mail->Password   = $config['smtp_pass'];
        $mail->SMTPSecure = $config['smtp_secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['smtp_port'];
        $mail->CharSet    = 'UTF-8';
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to_email, $nama);
        
        $mail->isHTML(true);
        $mail->Subject = 'E-Ticket Konfirmasi AidFest 2026 #' . str_pad($ticket['id_tiket'], 5, '0', STR_PAD_LEFT);
        
        $ticket_no = '#ADF-' . str_pad($ticket['id_tiket'], 5, '0', STR_PAD_LEFT);
        $formatted_price = number_format(get_ticket_price($ticket['kategori_tiket'], $ticket['paket_hari']), 0, ',', '.');
        $check_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/index.php?email_search=" . urlencode($to_email) . "#cek-tiket";
        
        $qr_data = "TICKET_ID: " . $ticket_no . "\n"
                 . "CODE: " . $ticket['kode_pembayaran'] . "\n"
                 . "NAME: " . $ticket['nama_pemesan'] . "\n"
                 . "CATEGORY: " . $ticket['kategori_tiket'] . "\n"
                 . "DAY: " . $ticket['paket_hari'];
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
        
        $mail->Body = '
        <div style="font-family: \'Outfit\', Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: white; padding: 40px 20px; text-align: center;">
                <h1 style="margin: 0; font-size: 28px; font-weight: 800;">AidFest 2026</h1>
                <p style="margin: 10px 0 0 0; color: #a5b4fc; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">E-Ticket Resmi Anda Telah Aktif</p>
            </div>
            <div style="padding: 30px; background-color: white;">
                <p style="margin-top: 0; font-size: 16px; color: #334155;">Halo <strong>' . htmlspecialchars($nama) . '</strong>,</p>
                <p style="font-size: 14px; color: #475569; line-height: 1.6;">Terima kasih telah menyelesaikan pembayaran. Pendaftaran tiket Anda untuk **AidFest 2026** telah berhasil diverifikasi dan berstatus **Lunas**.</p>
                
                <div style="background-color: #f1f5f9; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 25px; margin-top: 25px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #cbd5e1; padding-bottom: 8px;">Detail E-Ticket</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Nomor Tiket:</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold; color: #4f46e5;">' . $ticket_no . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Kategori Tiket:</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold;">' . htmlspecialchars($ticket['kategori_tiket']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Pilihan Hari:</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: bold;">' . htmlspecialchars($ticket['paket_hari']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Kode Transaksi:</td>
                            <td style="padding: 6px 0; text-align: right; font-family: monospace;">' . htmlspecialchars($ticket['kode_pembayaran']) . '</td>
                        </tr>
                        <tr style="border-top: 1px dashed #cbd5e1;">
                            <td style="padding: 12px 0 0 0; font-weight: bold;">Total Bayar:</td>
                            <td style="padding: 12px 0 0 0; text-align: right; font-weight: bold; font-size: 16px; color: #4f46e5;">Rp ' . $formatted_price . '</td>
                        </tr>
                    </table>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="margin-bottom: 20px; display: inline-block; padding: 10px; background-color: white; border: 1px solid #cbd5e1; border-radius: 8px;">
                        <img src="' . $qr_url . '" alt="QR Code E-Ticket" style="width: 130px; height: 130px; display: block;" />
                    </div>
                    <div style="margin-top: 10px;">
                        <a href="' . $check_url . '" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; text-decoration: none; padding: 12px 30px; font-weight: bold; border-radius: 8px; display: inline-block;">
                            Lihat & Cetak E-Ticket
                        </a>
                    </div>
                </div>
                
                <p style="font-size: 13px; color: #64748b; text-align: center; margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                    Harap simpan email ini dengan baik. Jangan bagikan QR Code atau nomor tiket Anda kepada orang lain.<br>
                    <strong>Sampai jumpa di AidFest 2026!</strong>
                </p>
            </div>
            <div style="background-color: #0f172a; color: #94a3b8; padding: 20px; text-align: center; font-size: 12px;">
                © 2026 AidFest. Gelora Bung Karno, Jakarta, Indonesia.
            </div>
        </div>
        ';
        
        $mail->AltBody = "Halo " . $nama . ",\n\nTerima kasih. Tiket Anda dengan nomor " . $ticket_no . " (" . $ticket['kategori_tiket'] . " - " . $ticket['paket_hari'] . ") telah berhasil dibayar (Lunas).\n\nTotal Bayar: Rp " . $formatted_price . "\n\nLihat E-Ticket Anda di: " . $check_url . "\n\nSampai jumpa di AidFest 2026!";
        
        return $mail->send();
    } catch (Exception $e) {
        $log_dir = __DIR__ . '/../scratch/';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        file_put_contents($log_dir . 'mail_error_log.txt', "[" . date('Y-m-d H:i:s') . "] Gagal kirim email ke $to_email. Error: " . $mail->ErrorInfo . "\n", FILE_APPEND);
        return false;
    }
}
?>
