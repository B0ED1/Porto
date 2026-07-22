<?php
// Memulai session
session_start();

// Hapus semua data session admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Kirim notifikasi sukses logout
$_SESSION['success'] = "Anda telah berhasil keluar dari sistem.";

// Arahkan ke halaman login admin (di folder yang sama)
header("Location: login.php");
exit();
?>
