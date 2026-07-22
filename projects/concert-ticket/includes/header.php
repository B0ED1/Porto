<?php
// Pastikan session sudah aktif sebelum memanggil is_admin_logged_in()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load file fungsi helper untuk cek status login admin
require_once __DIR__ . '/functions.php';

// Inisialisasi base path jika belum diatur
if (!isset($base_path)) {
    $base_path = '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Manajemen Tiket Festival Musik AidFest - Platform Native PHP CRUD untuk pengelolaan pendaftaran tiket festival secara praktis dan responsif.">
    <title>AidFest - Portal Musik & Tiket Festival Musik</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons for Beautiful Visuals -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Style Sheet for AidFest (Premium Aesthetics) -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>
<body>
    <!-- Navbar Modern Glassmorphism -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-aidfest sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_path; ?>index.php" id="nav-brand-logo">
                <i class="fa-solid fa-compact-disc fa-spin text-info me-2 fs-4" style="animation-duration: 4s;"></i>
                <span class="navbar-brand-gradient">AidFest</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3" href="<?php echo $base_path; ?>index.php" id="nav-link-home">
                            <i class="fa-solid fa-house me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3" href="<?php echo $base_path; ?>index.php#cek-tiket" id="nav-link-cek">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Cek Tiket
                        </a>
                    </li>
                    <?php if (is_admin_logged_in()): ?>
                        <li class="nav-item me-2">
                            <a class="nav-link text-white-50 px-3" href="<?php echo $base_path; ?>admin/index.php" id="nav-link-dashboard">
                                <i class="fa-solid fa-gauge me-1"></i> Admin Panel
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="btn btn-outline-danger btn-sm px-3" href="<?php echo $base_path; ?>admin/logout.php" id="nav-btn-logout">
                                <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="btn btn-premium-primary" href="<?php echo $base_path; ?>create.php" id="nav-btn-pesan">
                            <i class="fa-solid fa-ticket me-2"></i>Pesan Tiket
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
