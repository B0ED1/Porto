<?php
session_start();
require_once 'includes/functions.php';

$nama_pemesan = '';
$email = '';
$kategori_tiket = isset($_GET['kategori']) && in_array($_GET['kategori'], ['Reguler', 'VIP', 'VVIP']) ? $_GET['kategori'] : 'Reguler';
$paket_hari = isset($_GET['hari']) && in_array($_GET['hari'], ['Day 1', 'Day 2', '2-Day Pass']) ? $_GET['hari'] : '2-Day Pass';
$status_pembayaran = 'Pending';
$metode_pembayaran = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan = trim($_POST['nama_pemesan']);
    $email = trim($_POST['email']);
    $kategori_tiket = isset($_POST['kategori_tiket']) ? $_POST['kategori_tiket'] : 'Reguler';
    $paket_hari = isset($_POST['paket_hari']) ? $_POST['paket_hari'] : '2-Day Pass';
    
    if (is_admin_logged_in()) {
        $status_pembayaran = isset($_POST['status_pembayaran']) ? $_POST['status_pembayaran'] : 'Pending';
        $metode_pembayaran = isset($_POST['metode_pembayaran']) ? trim($_POST['metode_pembayaran']) : '';
    }

    if (empty($nama_pemesan)) {
        $errors['nama_pemesan'] = "Nama lengkap wajib diisi.";
    } elseif (strlen($nama_pemesan) < 3) {
        $errors['nama_pemesan'] = "Nama lengkap minimal harus 3 karakter.";
    }

    if (empty($email)) {
        $errors['email'] = "Alamat email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid.";
    }

    if (!in_array($kategori_tiket, ['Reguler', 'VIP', 'VVIP'])) {
        $errors['kategori_tiket'] = "Kategori tiket yang dipilih tidak valid.";
    }

    if (!in_array($paket_hari, ['Day 1', 'Day 2', '2-Day Pass'])) {
        $errors['paket_hari'] = "Pilihan hari tiket tidak valid.";
    }

    // Validasi VVIP hanya untuk 2-Day Pass
    if ($kategori_tiket === 'VVIP' && $paket_hari !== '2-Day Pass') {
        $errors['paket_hari'] = "Tiket kategori VVIP hanya tersedia untuk akses penuh Terusan 2 Hari (2-Day Pass).";
    }

    if (empty($errors)) {
        if (is_email_registered($pdo, $email)) {
            $errors['email'] = "Alamat email ini sudah terdaftar untuk pemesanan tiket.";
        }
    }

    if (empty($errors)) {
        $kode_pembayaran = ($status_pembayaran === 'Lunas') ? 'TRX-' . mt_rand(10000, 99999) : null;
        $final_metode = ($status_pembayaran === 'Lunas') ? (empty($metode_pembayaran) ? 'Manual Admin' : $metode_pembayaran) : null;

        $ticket_id = create_ticket($pdo, $nama_pemesan, $email, $kategori_tiket, $paket_hari, $status_pembayaran, $final_metode, $kode_pembayaran);
        
        if ($ticket_id) {
            if (is_admin_logged_in()) {
                $_SESSION['success'] = "Tiket atas nama <strong>" . htmlspecialchars($nama_pemesan) . "</strong> (" . $kategori_tiket . " - " . $paket_hari . ") berhasil ditambahkan!";
                header("Location: admin/index.php");
            } else {
                $_SESSION['last_booking_id'] = $ticket_id;
                header("Location: payment.php");
            }
            exit();
        } else {
            $errors['db'] = "Gagal memproses pemesanan tiket. Silakan coba kembali.";
        }
    }
}

$base_path = '';
include_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="form-card-container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <?php if (is_admin_logged_in()): ?>
                    <li class="breadcrumb-item"><a href="admin/index.php" class="text-decoration-none text-indigo"><i class="fa-solid fa-house me-1"></i>Dashboard Admin</a></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-indigo"><i class="fa-solid fa-house me-1"></i>Beranda</a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">Pesan Tiket</li>
            </ol>
        </nav>

        <!-- Form Card Wrapper -->
        <div class="form-card">
            <!-- Header Form dengan style Gradasi Premium -->
            <div class="form-card-header">
                <h3 class="fw-bold mb-1"><i class="fa-solid fa-ticket-simple me-2"></i>Pesan Tiket AidFest</h3>
                <p class="mb-0 opacity-75 fs-6">Silakan isi formulir di bawah ini dengan lengkap untuk mendapatkan tiket Anda.</p>
            </div>
            
            <!-- Body Form -->
            <div class="form-card-body">
                <!-- Banner error jika ada validasi database/server gagal -->
                <?php if (isset($errors['db'])): ?>
                    <div class="alert alert-danger alert-aidfest d-flex align-items-center mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                        <div><?php echo $errors['db']; ?></div>
                    </div>
                <?php endif; ?>

                <form action="create.php" method="POST" class="needs-validation" novalidate id="form-create-tiket">
                    <!-- Input Nama -->
                    <div class="mb-4">
                        <label for="nama_pemesan" class="form-label fw-semibold text-slate-700">Nama Lengkap Pemesan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-slate-500"><i class="fa-solid fa-user"></i></span>
                            <input type="text" 
                                   name="nama_pemesan" 
                                   id="nama_pemesan" 
                                   class="form-control <?php echo isset($errors['nama_pemesan']) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Contoh: Rian Hidayat" 
                                   value="<?php echo htmlspecialchars($nama_pemesan); ?>" 
                                   required>
                            <?php if (isset($errors['nama_pemesan'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['nama_pemesan']; ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Nama pemesan wajib diisi (minimal 3 karakter).</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Input Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-slate-700">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-slate-500"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   placeholder="Contoh: rian@example.com" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Alamat email wajib diisi dengan format email yang valid.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Input Kategori Tiket -->
                    <div class="mb-4">
                        <label for="kategori_tiket" class="form-label fw-semibold text-slate-700">Kategori Tiket</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-slate-500"><i class="fa-solid fa-crown"></i></span>
                            <select name="kategori_tiket" 
                                    id="kategori_tiket" 
                                    class="form-select <?php echo isset($errors['kategori_tiket']) ? 'is-invalid' : ''; ?>" 
                                    required>
                                <option value="Reguler" <?php echo $kategori_tiket === 'Reguler' ? 'selected' : ''; ?>>Reguler (Standard Experience)</option>
                                <option value="VIP" <?php echo $kategori_tiket === 'VIP' ? 'selected' : ''; ?>>VIP (Front Row Access)</option>
                                <option value="VVIP" <?php echo $kategori_tiket === 'VVIP' ? 'selected' : ''; ?>>VVIP (Backstage Lounge Access)</option>
                            </select>
                            <?php if (isset($errors['kategori_tiket'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['kategori_tiket']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Input Pilihan Hari -->
                    <div class="mb-4">
                        <label for="paket_hari" class="form-label fw-semibold text-slate-700">Pilihan Hari Acara</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-slate-500"><i class="fa-solid fa-calendar-day"></i></span>
                            <select name="paket_hari" 
                                    id="paket_hari" 
                                    class="form-select <?php echo isset($errors['paket_hari']) ? 'is-invalid' : ''; ?>" 
                                    required>
                                <option value="Day 1" <?php echo $paket_hari === 'Day 1' ? 'selected' : ''; ?>>Day 1 Only (Sabtu, 15 Agt)</option>
                                <option value="Day 2" <?php echo $paket_hari === 'Day 2' ? 'selected' : ''; ?>>Day 2 Only (Minggu, 16 Agt)</option>
                                <option value="2-Day Pass" <?php echo $paket_hari === '2-Day Pass' ? 'selected' : ''; ?>>2-Day Pass (Akses Terusan 2 Hari)</option>
                            </select>
                            <?php if (isset($errors['paket_hari'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['paket_hari']; ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Pilihan hari wajib ditentukan (VVIP hanya untuk 2-Day Pass).</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- PENGATURAN KHUSUS ADMIN (Hanya Tampil Jika Logged In) -->
                    <?php if (is_admin_logged_in()): ?>
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <h6 class="fw-bold text-slate-800 border-bottom pb-2 mb-3"><i class="fa-solid fa-user-shield me-2 text-indigo"></i>Opsi Administrator</h6>
                            
                            <!-- Status Pembayaran -->
                            <div class="mb-3">
                                <label for="status_pembayaran" class="form-label fw-semibold text-slate-700">Status Pembayaran</label>
                                <select name="status_pembayaran" id="status_pembayaran" class="form-select">
                                    <option value="Pending" <?php echo $status_pembayaran === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Lunas" <?php echo $status_pembayaran === 'Lunas' ? 'selected' : ''; ?>>Lunas</option>
                                    <option value="Batal" <?php echo $status_pembayaran === 'Batal' ? 'selected' : ''; ?>>Batal</option>
                                </select>
                            </div>
                            
                            <!-- Metode Pembayaran -->
                            <div>
                                <label for="metode_pembayaran" class="form-label fw-semibold text-slate-700">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-select">
                                    <option value="" <?php echo $metode_pembayaran === '' ? 'selected' : ''; ?>>— Pilih Metode (Opsional) —</option>
                                    <option value="Transfer Bank BCA" <?php echo $metode_pembayaran === 'Transfer Bank BCA' ? 'selected' : ''; ?>>Transfer Bank BCA</option>
                                    <option value="Transfer Bank Mandiri" <?php echo $metode_pembayaran === 'Transfer Bank Mandiri' ? 'selected' : ''; ?>>Transfer Bank Mandiri</option>
                                    <option value="GoPay" <?php echo $metode_pembayaran === 'GoPay' ? 'selected' : ''; ?>>GoPay</option>
                                    <option value="Dana" <?php echo $metode_pembayaran === 'Dana' ? 'selected' : ''; ?>>DANA</option>
                                    <option value="Tunai / Cash" <?php echo $metode_pembayaran === 'Tunai / Cash' ? 'selected' : ''; ?>>Tunai / Cash</option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tombol Aksi Form -->
                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-premium-primary py-2" id="submit-create-btn">
                            <i class="fa-solid fa-paper-plane me-2"></i><?php echo is_admin_logged_in() ? 'Tambah Tiket' : 'Lanjutkan ke Pembayaran'; ?>
                        </button>
                        <a href="<?php echo is_admin_logged_in() ? 'admin/index.php' : 'index.php'; ?>" class="btn btn-outline-secondary py-2" id="cancel-create-btn">
                            Batal & Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Load Footer Global
include_once 'includes/footer.php'; 
?>
