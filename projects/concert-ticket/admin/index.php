<?php
session_start();
require_once '../includes/functions.php';
require_admin_login();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$pendaftaran = get_all_tickets($pdo, $search);
$stats = get_ticket_statistics($pdo);
$base_path = '../';

include_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-slate-800 mb-1"><i class="fa-solid fa-gauge text-indigo me-2"></i>Dashboard Admin</h2>
            <p class="text-muted mb-0">Kelola dan pantau seluruh pendaftaran tiket festival AidFest 2026.</p>
        </div>
        <div>
            <a href="../create.php" class="btn btn-premium-primary" id="btn-tambah-pemesan">
                <i class="fa-solid fa-plus me-2"></i>Tambah Pemesan Baru
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-aidfest d-flex align-items-center mb-4" role="alert">
            <i class="fa-solid fa-circle-check fs-5 me-2"></i>
            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-aidfest d-flex align-items-center mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md col-sm-6">
            <div class="stat-card total p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Total Pendaftar</span>
                        <h4 class="fw-bold mb-0 text-slate-800"><?php echo $stats['total']; ?></h4>
                    </div>
                    <div class="stat-icon total" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-6">
            <div class="stat-card revenue p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Pendapatan (Lunas)</span>
                        <h4 class="fw-bold mb-0 text-slate-800">Rp <?php echo number_format($stats['pendapatan'], 0, ',', '.'); ?></h4>
                    </div>
                    <div class="stat-icon revenue" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-4">
            <div class="stat-card reguler p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Reguler</span>
                        <h4 class="fw-bold mb-0 text-slate-800"><?php echo $stats['reguler']; ?></h4>
                    </div>
                    <div class="stat-icon reguler" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-4">
            <div class="stat-card vip p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">VIP</span>
                        <h4 class="fw-bold mb-0 text-slate-800"><?php echo $stats['vip']; ?></h4>
                    </div>
                    <div class="stat-icon vip" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md col-sm-4">
            <div class="stat-card vvip p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">VVIP</span>
                        <h4 class="fw-bold mb-0 text-slate-800"><?php echo $stats['vvip']; ?></h4>
                    </div>
                    <div class="stat-icon vvip" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="fa-solid fa-gem"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3 border shadow-sm p-4">
        
        <!-- Filter Pencarian -->
        <div class="row justify-content-end mb-4">
            <div class="col-md-5">
                <form action="index.php" method="GET">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama atau email..." value="<?php echo htmlspecialchars($search); ?>" id="search-input">
                        <?php if ($search !== ''): ?>
                            <a href="index.php" class="btn btn-outline-secondary" type="button"><i class="fa-solid fa-xmark"></i></a>
                        <?php endif; ?>
                        <button class="btn btn-premium-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Responsif -->
        <div class="table-responsive">
            <table class="table table-aidfest" id="ticket-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Nama Pemesan</th>
                        <th>Alamat Email</th>
                        <th>Kategori Tiket</th>
                        <th>Paket Hari</th>
                        <th>Status</th>
                        <th>Detail Transaksi</th>
                        <th>Tanggal Pesan</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pendaftaran) > 0): ?>
                        <?php $no = 1; foreach ($pendaftaran as $row): ?>
                            <tr>
                                <td><span class="text-muted fw-medium"><?php echo $no++; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($row['nama_pemesan'], 0, 1)); ?>
                                        </div>
                                        <span class="fw-semibold text-slate-800"><?php echo htmlspecialchars($row['nama_pemesan']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-slate-600"><i class="fa-regular fa-envelope me-1 text-muted"></i><?php echo htmlspecialchars($row['email']); ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $badgeClass = '';
                                    if ($row['kategori_tiket'] == 'Reguler') $badgeClass = 'badge-reguler';
                                    elseif ($row['kategori_tiket'] == 'VIP') $badgeClass = 'badge-vip';
                                    elseif ($row['kategori_tiket'] == 'VVIP') $badgeClass = 'badge-vvip';
                                    ?>
                                    <span class="badge badge-category <?php echo $badgeClass; ?>">
                                        <?php echo $row['kategori_tiket']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-slate-700"><?php echo htmlspecialchars($row['paket_hari']); ?></span>
                                </td>
                                <td>
                                    <?php if ($row['status_pembayaran'] === 'Lunas'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i>Lunas</span>
                                    <?php elseif ($row['status_pembayaran'] === 'Pending'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill"><i class="fa-solid fa-clock me-1"></i>Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-xmark me-1"></i>Batal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status_pembayaran'] === 'Lunas'): ?>
                                        <div class="text-slate-700" style="font-size: 0.85rem;">
                                            <span class="d-block fw-semibold"><i class="fa-solid fa-circle-dot text-indigo me-1"></i><?php echo htmlspecialchars($row['metode_pembayaran']); ?></span>
                                            <span class="text-muted text-uppercase" style="font-size: 0.75rem;"><?php echo htmlspecialchars($row['kode_pembayaran']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.85rem;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-slate-500" style="font-size: 0.85rem;">
                                        <i class="fa-regular fa-calendar-days me-1 text-muted"></i><?php echo format_tanggal_indo($row['tanggal_pesan']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="edit.php?id=<?php echo $row['id_tiket']; ?>" class="btn-action edit" title="Edit Data" id="edit-btn-<?php echo $row['id_tiket']; ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id_tiket']; ?>" class="btn-action delete btn-delete-confirm" data-name="<?php echo htmlspecialchars($row['nama_pemesan']); ?>" title="Hapus Data" id="delete-btn-<?php echo $row['id_tiket']; ?>">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-regular fa-folder-open fs-1 mb-3 text-slate-300 d-block"></i>
                                    <?php if ($search !== ''): ?>
                                        Tidak ditemukan pemesan tiket dengan kata kunci "<strong><?php echo htmlspecialchars($search); ?></strong>".
                                    <?php else: ?>
                                        Belum ada pemesan tiket terdaftar. Klik <strong>"Tambah Pemesan Baru"</strong> untuk memulai.
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Load Footer Global dari subfolder
include_once '../includes/footer.php'; 
?>
