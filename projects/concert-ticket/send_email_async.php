<?php
// send_email_async.php
// Skrip pemrosesan pengiriman email E-Ticket secara asinkron (background process)

session_start();
require_once 'includes/functions.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit();
}

$ticket = get_ticket_by_id($pdo, $id);
if (!$ticket) {
    echo json_encode(['status' => 'error', 'message' => 'Tiket tidak ditemukan']);
    exit();
}

// Keamanan: Hanya kirim email jika status tiket sudah Lunas
if ($ticket['status_pembayaran'] !== 'Lunas') {
    echo json_encode(['status' => 'error', 'message' => 'Tiket belum lunas']);
    exit();
}

// Proses pengiriman email
$success = send_ticket_email($ticket['email'], $ticket['nama_pemesan'], $ticket);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Email berhasil dikirim di latar belakang']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email']);
}
