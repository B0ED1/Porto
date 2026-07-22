<?php
// Memulai session
session_start();

// Memuat file fungsi helper
require_once 'includes/functions.php';

// Membaca ID tiket dari GET atau dari session booking terakhir
$id = 0;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} elseif (isset($_SESSION['last_booking_id'])) {
    $id = (int)$_SESSION['last_booking_id'];
}

// Validasi ID tiket
if ($id <= 0) {
    $_SESSION['error'] = "Tidak ada transaksi pembayaran yang aktif.";
    header("Location: index.php");
    exit();
}

// Mengambil data tiket dari database
$ticket = get_ticket_by_id($pdo, $id);
if (!$ticket) {
    $_SESSION['error'] = "Data transaksi tiket tidak ditemukan.";
    header("Location: index.php");
    exit();
}

// Jika tiket sudah lunas, langsung arahkan ke E-ticket pembeli di index.php
if ($ticket['status_pembayaran'] === 'Lunas') {
    header("Location: index.php?email_search=" . urlencode($ticket['email']) . "#cek-tiket");
    exit();
}

$harga = get_ticket_price($ticket['kategori_tiket'], $ticket['paket_hari']);

$error = '';

// Aksi 1: Reset Metode Pembayaran (Batal ke Langkah 1)
if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
    update_payment_status($pdo, $id, 'Pending', null, null);
    header("Location: payment.php?id=" . $id);
    exit();
}

// Aksi 2: Konfirmasi Verifikasi Simulasi (POST dari JavaScript setelah animasi spinner selesai)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_simulation'])) {
    $kode_pembayaran = $ticket['kode_pembayaran'] ?: 'TRX-' . mt_rand(10000, 99999);
    $metode = $ticket['metode_pembayaran'];
    
    if (update_payment_status($pdo, $id, 'Lunas', $metode, $kode_pembayaran)) {
        if (isset($_SESSION['last_booking_id'])) {
            unset($_SESSION['last_booking_id']);
        }
        
        // Ambil data terbaru
        $updated_ticket = get_ticket_by_id($pdo, $id);
        $email_address = $updated_ticket ? $updated_ticket['email'] : $ticket['email'];
        $kategori_tiket = $updated_ticket ? $updated_ticket['kategori_tiket'] : $ticket['kategori_tiket'];

        // Set session ID agar halaman index.php memicu kirim email asinkron di latar belakang
        $_SESSION['send_email_id'] = $id;
        
        $_SESSION['success'] = "Pembayaran sukses! E-Ticket Anda untuk kategori <strong>" . $kategori_tiket . "</strong> kini aktif dan salinannya akan segera dikirim ke email <strong>" . htmlspecialchars($email_address) . "</strong>.";
        header("Location: index.php?email_search=" . urlencode($email_address) . "#cek-tiket");
        exit();
    } else {
        $error = "Terjadi kesalahan sistem saat memproses verifikasi. Silakan coba lagi.";
    }
}

// Aksi 3: Submit Pilihan Metode Pembayaran (Langkah 1 ke Langkah 2)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['confirm_simulation'])) {
    $metode = isset($_POST['metode_pembayaran']) ? trim($_POST['metode_pembayaran']) : '';

    if (empty($metode)) {
        $error = "Silakan pilih salah satu metode pembayaran.";
    } else {
        $kode_pembayaran = 'TRX-' . mt_rand(10000, 99999);
        
        if (update_payment_status($pdo, $id, 'Pending', $metode, $kode_pembayaran)) {
            header("Location: payment.php?id=" . $id);
            exit();
        } else {
            $error = "Terjadi kesalahan sistem saat menyimpan metode pembayaran.";
        }
    }
}

// Set base path
$base_path = '';

// Load Header Global
include_once 'includes/header.php';

// Deteksi apakah sedang dalam mode Instruksi Pembayaran (Langkah 2)
$show_simulation = ($ticket['status_pembayaran'] === 'Pending' && !empty($ticket['metode_pembayaran']));
?>

<div class="container my-5">
    <div class="form-card-container" style="max-width: 600px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4 no-print">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-indigo"><i class="fa-solid fa-house me-1"></i>Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pembayaran Tiket</li>
            </ol>
        </nav>

        <div class="form-card">
            <?php if (!$show_simulation): ?>
                <!-- LANGKAH 1: PILIH METODE PEMBAYARAN -->
                <div class="form-card-header" style="background: linear-gradient(135deg, #0f172a 0%, #312e81 100%);">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2"><i class="fa-solid fa-clock-rotate-left me-1"></i>Menunggu Pembayaran</span>
                    <h3 class="fw-bold mb-0">Checkout Pembayaran</h3>
                    <p class="mb-0 opacity-75 fs-6 mt-1">Selesaikan pembayaran untuk mengaktifkan E-Ticket Anda.</p>
                </div>

                <div class="form-card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-aidfest d-flex align-items-center mb-4" role="alert">
                            <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                            <div><?php echo $error; ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Detail Tagihan -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <h6 class="fw-bold text-slate-800 border-bottom pb-2 mb-3"><i class="fa-solid fa-file-invoice-dollar me-2 text-indigo"></i>Rincian Tagihan</h6>
                        
                        <div class="row mb-2">
                            <div class="col-6 text-slate-500">Nama Pemesan</div>
                            <div class="col-6 text-end fw-semibold text-slate-800"><?php echo htmlspecialchars($ticket['nama_pemesan']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-slate-500">Alamat Email</div>
                            <div class="col-6 text-end text-slate-800" style="font-size: 0.9rem;"><?php echo htmlspecialchars($ticket['email']); ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-slate-500">Kategori Tiket</div>
                            <div class="col-6 text-end">
                                <?php 
                                $badgeClass = '';
                                if ($ticket['kategori_tiket'] == 'Reguler') $badgeClass = 'badge-reguler';
                                elseif ($ticket['kategori_tiket'] == 'VIP') $badgeClass = 'badge-vip';
                                elseif ($ticket['kategori_tiket'] == 'VVIP') $badgeClass = 'badge-vvip';
                                ?>
                                <span class="badge badge-category <?php echo $badgeClass; ?>"><?php echo $ticket['kategori_tiket']; ?></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-slate-500">Pilihan Hari</div>
                            <div class="col-6 text-end fw-semibold text-slate-800"><?php echo htmlspecialchars($ticket['paket_hari']); ?></div>
                        </div>
                        
                        <div class="border-top my-3"></div>
                        
                        <div class="row align-items-center">
                            <div class="col-6 fw-bold text-slate-700">Total Pembayaran</div>
                            <div class="col-6 text-end fw-bold text-indigo fs-4">Rp <?php echo number_format($harga, 0, ',', '.'); ?></div>
                        </div>
                    </div>

                    <form action="payment.php?id=<?php echo $id; ?>" method="POST" id="form-pembayaran">
                        <h6 class="fw-bold text-slate-800 mb-3"><i class="fa-solid fa-credit-card me-2 text-indigo"></i>Pilih Metode Pembayaran</h6>
                        
                        <div class="mb-3">
                            <div class="form-check p-3 border rounded-3 d-flex align-items-center cursor-pointer mb-2 hover-bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="metode_pembayaran" id="metode_bca" value="Transfer Bank BCA" required>
                                <label class="form-check-label w-100 cursor-pointer d-flex align-items-center justify-content-between" for="metode_bca">
                                    <span class="fw-semibold text-slate-800"><i class="fa-solid fa-building-columns me-2 text-muted"></i>Transfer Bank BCA</span>
                                    <span class="text-muted" style="font-size: 0.8rem;">Virtual Account</span>
                                </label>
                            </div>
                            
                            <div class="form-check p-3 border rounded-3 d-flex align-items-center cursor-pointer mb-2 hover-bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="metode_pembayaran" id="metode_mandiri" value="Transfer Bank Mandiri">
                                <label class="form-check-label w-100 cursor-pointer d-flex align-items-center justify-content-between" for="metode_mandiri">
                                    <span class="fw-semibold text-slate-800"><i class="fa-solid fa-building-columns me-2 text-muted"></i>Transfer Bank Mandiri</span>
                                    <span class="text-muted" style="font-size: 0.8rem;">Virtual Account</span>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded-3 d-flex align-items-center cursor-pointer mb-2 hover-bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="metode_pembayaran" id="metode_gopay" value="GoPay">
                                <label class="form-check-label w-100 cursor-pointer d-flex align-items-center justify-content-between" for="metode_gopay">
                                    <span class="fw-semibold text-slate-800"><i class="fa-solid fa-wallet me-2 text-muted"></i>GoPay</span>
                                    <span class="text-muted" style="font-size: 0.8rem;">Scan QRIS</span>
                                </label>
                            </div>
                            
                            <div class="form-check p-3 border rounded-3 d-flex align-items-center cursor-pointer mb-2 hover-bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="metode_pembayaran" id="metode_dana" value="Dana">
                                <label class="form-check-label w-100 cursor-pointer d-flex align-items-center justify-content-between" for="metode_dana">
                                    <span class="fw-semibold text-slate-800"><i class="fa-solid fa-wallet me-2 text-muted"></i>DANA</span>
                                    <span class="text-muted" style="font-size: 0.8rem;">Nomor Telepon</span>
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 bg-light-subtle text-slate-600 mb-4" style="font-size: 0.85rem; border-left: 4px solid #0dcaf0 !important;">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            <strong>Catatan Simulasi:</strong> Halaman ini mensimulasikan sistem pembayaran asli. Pilih metode pembayaran, lalu tekan tombol <strong>Lanjutkan</strong>.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-premium-primary py-2 btn-lg fs-6" id="confirm-payment-btn">
                                Lanjutkan Pembayaran<i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary py-2" id="cancel-payment-btn">
                                Bayar Nanti & Kembali
                            </a>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- LANGKAH 2: INSTRUKSI PEMBAYARAN & SIMULASI GATEWAY -->
                <div class="form-card-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="badge bg-danger text-white px-3 py-1 rounded-pill"><i class="fa-solid fa-spinner fa-spin me-1"></i>Menunggu Transfer</span>
                        <div class="text-end text-white">
                            <span style="font-size: 0.8rem;" class="opacity-75 d-block">Sisa Waktu</span>
                            <span id="payment-timer" class="fw-bold fs-5 text-warning font-monospace">10:00</span>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 mt-3"><i class="fa-solid fa-shield-halved text-info me-2"></i>Gerbang Pembayaran</h3>
                    <p class="mb-0 opacity-75 fs-6 mt-1">Selesaikan transfer melalui <?php echo htmlspecialchars($ticket['metode_pembayaran']); ?></p>
                </div>

                <div class="form-card-body">
                    <!-- Total Tagihan -->
                    <div class="text-center py-4 bg-light rounded-3 border mb-4">
                        <span class="text-muted small text-uppercase tracking-wider">Jumlah yang Harus Dibayar</span>
                        <h2 class="fw-bold text-indigo mb-1 mt-1">Rp <?php echo number_format($harga, 0, ',', '.'); ?></h2>
                        <span class="badge bg-indigo-subtle text-indigo px-3 py-1 mt-2">Kode Pembayaran: <?php echo htmlspecialchars($ticket['kode_pembayaran']); ?></span>
                    </div>

                    <!-- Layout Instruksi Sesuai Metode -->
                    <?php if ($ticket['metode_pembayaran'] === 'GoPay' || $ticket['metode_pembayaran'] === 'Dana'): ?>
                        <!-- QRIS Code simulation -->
                        <div class="text-center mb-4 p-3 border rounded-3 bg-white">
                            <h6 class="fw-bold text-slate-800 mb-2"><i class="fa-solid fa-qrcode text-indigo me-2"></i>Pindai QRIS</h6>
                            <p class="text-muted small mb-3">Scan kode QR di bawah menggunakan e-wallet Anda.</p>
                            
                            <div class="d-inline-block p-2 border rounded bg-white shadow-sm mb-3">
                                <?php
                                $qr_data = "AidFest-Ticket-" . $ticket['id_tiket'] . "-Code-" . $ticket['kode_pembayaran'] . "-Amount-" . $harga;
                                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);
                                ?>
                                <img src="<?php echo $qr_url; ?>" alt="QRIS Code" style="width: 200px; height: 200px; display: block;" class="mx-auto img-fluid">
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-2 small text-muted">
                                <i class="fa-solid fa-lock text-success"></i>
                                <span>QR Code ini aman & dinonaktifkan otomatis setelah terbayar.</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Virtual Account simulation -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-slate-800 mb-3"><i class="fa-solid fa-circle-info text-indigo me-2"></i>Nomor Virtual Account</h6>
                            
                            <?php
                            $va_prefix = ($ticket['metode_pembayaran'] === 'Transfer Bank BCA') ? '80012' : '88790';
                            $va_number = $va_prefix . str_pad($ticket['id_tiket'], 5, '0', STR_PAD_LEFT);
                            ?>
                            <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small d-block"><?php echo htmlspecialchars($ticket['metode_pembayaran']); ?> Virtual Account</span>
                                    <strong id="va-number" class="fs-4 text-slate-800 tracking-wide font-monospace"><?php echo $va_number; ?></strong>
                                </div>
                                <button type="button" class="btn btn-outline-indigo btn-sm fw-semibold" id="btn-copy-va" onclick="copyToClipboard('<?php echo $va_number; ?>')">
                                    <i class="fa-solid fa-copy me-1"></i>Salin VA
                                </button>
                            </div>
                        </div>

                        <!-- Petunjuk Singkat -->
                        <div class="p-3 border rounded-3 bg-white mb-4">
                            <h6 class="fw-bold text-slate-800 mb-2 small text-uppercase tracking-wider">Petunjuk Pembayaran</h6>
                            <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                <li>Masuk ke Mobile Banking atau kunjungi mesin ATM terdekat.</li>
                                <li>Pilih menu <strong>Transfer</strong> > <strong>Virtual Account</strong>.</li>
                                <li>Masukkan nomor Virtual Account di atas dan klik Lanjutkan.</li>
                                <li>Pastikan detail nama <strong>AidFest - <?php echo htmlspecialchars($ticket['nama_pemesan']); ?></strong> dan nominalnya sesuai.</li>
                            </ol>
                        </div>
                    <?php endif; ?>

                    <!-- Form Verifikasi Simulasi -->
                    <form action="payment.php?id=<?php echo $id; ?>" method="POST" id="form-verifikasi-simulasi">
                        <input type="hidden" name="confirm_simulation" value="1">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-premium-primary py-2 btn-lg fs-6" id="btn-verifikasi-pembayaran">
                                <i class="fa-solid fa-circle-check me-2"></i>Konfirmasi Pembayaran
                            </button>
                            <a href="payment.php?id=<?php echo $id; ?>&reset=true" class="btn btn-outline-secondary py-2" id="btn-ganti-metode">
                                <i class="fa-solid fa-arrow-rotate-left me-2"></i>Ganti Metode Pembayaran
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- OVERLAY LOADING ANIMATION UNTUK SIMULASI -->
<div id="loading-overlay" class="d-none" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; flex-direction: column;">
    <div id="loader-spinner" class="spinner-border text-info mb-3" role="status" style="width: 3.5rem; height: 3.5rem;"></div>
    <div id="loader-icon-success" class="d-none mb-3">
        <i class="fa-solid fa-circle-check text-success display-3 animate-tick"></i>
    </div>
    <h4 id="loading-status" class="fw-bold tracking-tight">Menghubungkan ke bank...</h4>
    <p class="text-white-50 small mt-1">Sistem sedang mendeteksi pembayaran dari server.</p>
</div>

<!-- CSS inline khusus untuk efek tick mark -->
<style>
.animate-tick {
    animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}
@keyframes popIn {
    0% { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
.hover-bg-light:hover {
    background-color: #f8fafc !important;
    border-color: #818cf8 !important;
}
</style>

<!-- JS Kustom untuk Simulasi Pembayaran -->
<script>
// Fungsi menyalin nomor VA
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        const btn = document.getElementById('btn-copy-va');
        btn.innerHTML = '<i class="fa-solid fa-check me-1"></i>Tersalin!';
        btn.className = 'btn btn-success btn-sm fw-semibold';
        setTimeout(() => {
            btn.innerHTML = '<i class="fa-solid fa-copy me-1"></i>Salin VA';
            btn.className = 'btn btn-outline-indigo btn-sm fw-semibold';
        }, 2000);
    }, function() {
        alert('Gagal menyalin nomor.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Timer Hitung Mundur (10 Menit) untuk Langkah 2
    const timerElement = document.getElementById('payment-timer');
    if (timerElement) {
        let totalSeconds = 600; // 10 menit
        const countdown = setInterval(() => {
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;
            
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            timerElement.textContent = `${minutes}:${seconds}`;
            
            if (totalSeconds <= 0) {
                clearInterval(countdown);
                alert('Waktu pembayaran telah habis. Silakan buat pendaftaran ulang.');
                window.location.href = 'index.php';
            }
            
            totalSeconds--;
        }, 1000);
    }

    // 2. Animasi Spinner & Proses Simulasi Verifikasi
    const formVerifikasi = document.getElementById('form-verifikasi-simulasi');
    const overlay = document.getElementById('loading-overlay');
    const statusText = document.getElementById('loading-status');
    const spinner = document.getElementById('loader-spinner');
    const successIcon = document.getElementById('loader-icon-success');

    if (formVerifikasi) {
        formVerifikasi.addEventListener('submit', (e) => {
            e.preventDefault(); // cegah submit langsung
            
            // Munculkan overlay loading
            overlay.classList.remove('d-none');
            
            // Tahap 1: Hubungkan ke server
            setTimeout(() => {
                statusText.textContent = "Memverifikasi transaksi pembayaran...";
                
                // Tahap 2: Deteksi status sukses
                setTimeout(() => {
                    spinner.classList.add('d-none');
                    successIcon.classList.remove('d-none');
                    statusText.textContent = "Pembayaran Berhasil! Mengirimkan E-Ticket...";
                    
                    // Tahap 3: Submit data ke database
                    setTimeout(() => {
                        formVerifikasi.submit();
                    }, 500);
                }, 800);
            }, 500);
        });
    }
});
</script>

<?php 
// Load Footer Global
include_once 'includes/footer.php'; 
?>
