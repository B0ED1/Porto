<?php
session_start();
require_once '../includes/functions.php';
require_admin_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = "ID tiket tidak valid atau tidak disertakan.";
    header("Location: index.php");
    exit();
}

$ticket = get_ticket_by_id($pdo, $id);

if (!$ticket) {
    $_SESSION['error'] = "Data tiket dengan ID tersebut tidak ditemukan.";
    header("Location: index.php");
    exit();
}

$nama_pemesan = $ticket['nama_pemesan'];
$email = $ticket['email'];
$kategori_tiket = $ticket['kategori_tiket'];
$paket_hari = $ticket['paket_hari'];
$status_pembayaran = $ticket['status_pembayaran'];
$metode_pembayaran = $ticket['metode_pembayaran'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan = trim($_POST['nama_pemesan']);
    $email = trim($_POST['email']);
    $kategori_tiket = isset($_POST['kategori_tiket']) ? $_POST['kategori_tiket'] : 'Reguler';
    $paket_hari = isset($_POST['paket_hari']) ? $_POST['paket_hari'] : '2-Day Pass';
    $status_pembayaran = isset($_POST['status_pembayaran']) ? $_POST['status_pembayaran'] : 'Pending';
    $metode_pembayaran = isset($_POST['metode_pembayaran']) ? trim($_POST['metode_pembayaran']) : '';

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

    if (!in_array($status_pembayaran, ['Pending', 'Lunas', 'Batal'])) {
        $errors['status_pembayaran'] = "Status pembayaran tidak valid.";
    }

    if (empty($errors)) {
        if (is_email_registered($pdo, $email, $id)) {
            $errors['email'] = "Alamat email ini sudah terdaftar untuk pemesan tiket lainnya.";
        }
    }

    if (empty($errors)) {
        $previous_status = $ticket['status_pembayaran'];
        $kode_pembayaran = $ticket['kode_pembayaran'];
        if ($status_pembayaran === 'Lunas') {
            if (empty($kode_pembayaran)) {
                $kode_pembayaran = 'TRX-' . mt_rand(10000, 99999);
            }
            if (empty($metode_pembayaran)) {
                $metode_pembayaran = 'Manual Admin';
            }
        } else {
            $metode_pembayaran = null;
            $kode_pembayaran = null;
        }

        if (update_ticket($pdo, $id, $nama_pemesan, $email, $kategori_tiket, $paket_hari, $status_pembayaran, $metode_pembayaran, $kode_pembayaran)) {
            // Kirim email jika status pembayaran diubah ke Lunas oleh admin
            if ($status_pembayaran === 'Lunas' && $previous_status !== 'Lunas') {
                $updated_ticket = get_ticket_by_id($pdo, $id);
                send_ticket_email($updated_ticket['email'], $updated_ticket['nama_pemesan'], $updated_ticket);
            }
            
            $_SESSION['success'] = "Data tiket atas nama <strong>" . htmlspecialchars($nama_pemesan) . "</strong> berhasil diperbarui!";
            header("Location: index.php");
            exit();
        } else {
            $errors['db'] = "Gagal memperbarui data tiket. Silakan coba kembali.";
        }
    }
}

$base_path = '../';
include_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="form-card-container">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-indigo"><i class="fa-solid fa-house me-1"></i>Dashboard Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Detail Tiket</li>
            </ol>
        </nav>

        <!-- Form Card Wrapper -->
        <div class="form-card">
            <!-- Header Form dengan style Gradasi Premium -->
            <div class="form-card-header" style="background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%);">
                <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-pen me-2"></i>Ubah Tiket AidFest</h3>
                <p class="mb-0 opacity-75 fs-6">Ubah informasi pendaftaran tiket untuk pemesan terpilih.</p>
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

                <form action="edit.php?id=<?php echo $id; ?>" method="POST" class="needs-validation" novalidate id="form-edit-tiket">
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
                                <option value="Reguler" <?php echo $kategori_tiket === 'Reguler' ? 'selected' : ''; ?>>Reguler (Standard Access)</option>
                                <option value="VIP" <?php echo $kategori_tiket === 'VIP' ? 'selected' : ''; ?>>VIP (Front Row + Merch)</option>
                                <option value="VVIP" <?php echo $kategori_tiket === 'VVIP' ? 'selected' : ''; ?>>VVIP (Backstage Access + Exclusive Lounge)</option>
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
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- PENGATURAN ADMINISTRATOR (Status Bayar) -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <h6 class="fw-bold text-slate-800 border-bottom pb-2 mb-3"><i class="fa-solid fa-user-shield me-2 text-indigo"></i>Status & Pembayaran (Admin)</h6>
                        
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
                                <option value="" <?php echo $metode_pembayaran === '' ? 'selected' : ''; ?>>— Pilih Metode (Jika Lunas) —</option>
                                <option value="Transfer Bank BCA" <?php echo $metode_pembayaran === 'Transfer Bank BCA' ? 'selected' : ''; ?>>Transfer Bank BCA</option>
                                <option value="Transfer Bank Mandiri" <?php echo $metode_pembayaran === 'Transfer Bank Mandiri' ? 'selected' : ''; ?>>Transfer Bank Mandiri</option>
                                <option value="GoPay" <?php echo $metode_pembayaran === 'GoPay' ? 'selected' : ''; ?>>GoPay</option>
                                <option value="Dana" <?php echo $metode_pembayaran === 'Dana' ? 'selected' : ''; ?>>DANA</option>
                                <option value="Tunai / Cash" <?php echo $metode_pembayaran === 'Tunai / Cash' ? 'selected' : ''; ?>>Tunai / Cash</option>
                                <option value="Manual Admin" <?php echo $metode_pembayaran === 'Manual Admin' ? 'selected' : ''; ?>>Manual Admin</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tombol Aksi Form -->
                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-premium-primary py-2" style="background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%); box-shadow: 0 4px 14px 0 rgba(124, 58, 237, 0.4);" id="submit-edit-btn">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary py-2" id="cancel-edit-btn">
                            Batal & Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Load Footer Global dari subfolder
include_once '../includes/footer.php'; 
?>
