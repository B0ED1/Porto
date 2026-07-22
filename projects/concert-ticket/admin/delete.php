<?php
// Memulai session untuk notifikasi alert
session_start();

// Memuat file fungsi helper dari subfolder
require_once '../includes/functions.php';

// Membatasi akses halaman hanya untuk admin yang login
require_admin_login();

// Membaca ID tiket dari parameter GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validasi ID tiket
if ($id <= 0) {
    $_SESSION['error'] = "ID tiket tidak valid untuk dihapus.";
    header("Location: index.php");
    exit();
}

// 1. Ambil data tiket terlebih dahulu untuk konfirmasi nama pemesan di alert banner
$ticket = get_ticket_by_id($pdo, $id);

if ($ticket) {
    // 2. Jalankan fungsi penghapusan
    if (delete_ticket($pdo, $id)) {
        $_SESSION['success'] = "Pendaftaran tiket atas nama <strong>" . htmlspecialchars($ticket['nama_pemesan']) . "</strong> telah berhasil dihapus dari sistem.";
    } else {
        $_SESSION['error'] = "Gagal menghapus data tiket. Silakan coba kembali.";
    }
} else {
    $_SESSION['error'] = "Data tiket tidak ditemukan atau sudah dihapus sebelumnya.";
}

// Kembali ke halaman dashboard utama (di folder yang sama)
header("Location: index.php");
exit();
?>
