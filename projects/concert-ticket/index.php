<?php
// Memulai session
session_start();

// Memuat file fungsi helper
require_once 'includes/functions.php';

// Memproses pencarian tiket pembeli
$search_email = isset($_GET['email_search']) ? trim($_GET['email_search']) : '';
$ticket_found = null;
$searched = false;

if ($search_email !== '') {
    $searched = true;
    $ticket_found = get_ticket_by_email($pdo, $search_email);
}

// Set base path untuk templating
$base_path = '';

// Load Header Global
include_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center d-flex align-items-center justify-content-center no-print">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge bg-indigo text-white px-3 py-2 rounded-pill mb-3" style="font-size: 0.85rem; letter-spacing: 1px;"><i class="fa-solid fa-calendar-days me-2"></i>15 - 16 AGUSTUS 2026</span>
                <h1 class="display-3 fw-bold mb-3 text-white tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    AidFest 2026
                </h1>
                <p class="lead text-white-50 mb-5 fs-4">Rasakan getaran musik festival terbesar tahun ini. Tampil memukau dengan artis-artis favorit Anda di panggung utama.</p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="#kategori-tiket" class="btn btn-premium-primary btn-lg px-4 py-3 fs-6">
                        <i class="fa-solid fa-ticket-simple me-2"></i>Pesan Tiket Sekarang
                    </a>
                    <a href="#cek-tiket" class="btn btn-outline-light btn-lg px-4 py-3 fs-6">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Cek Status Tiket
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lineup Section -->
<section class="container my-5 py-5 no-print" id="lineup">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-slate-900"><i class="fa-solid fa-guitar text-indigo me-2"></i>Artist Lineup</h2>
        <p class="text-muted">Jadwal penampilan bintang-bintang spektakuler di panggung utama AidFest 2026</p>
    </div>
    
    <!-- Tab Navigation -->
    <div class="d-flex justify-content-center mb-5">
        <ul class="nav nav-pills gap-3" id="lineupTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link nav-link-custom active" id="day1-tab" data-bs-toggle="tab" data-bs-target="#day1-pane" type="button" role="tab" aria-controls="day1-pane" aria-selected="true">
                    <i class="fa-solid fa-calendar-day me-2"></i>Day 1 (Sabtu, 15 Agt)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link nav-link-custom" id="day2-tab" data-bs-toggle="tab" data-bs-target="#day2-pane" type="button" role="tab" aria-controls="day2-pane" aria-selected="false">
                    <i class="fa-solid fa-calendar-day me-2"></i>Day 2 (Minggu, 16 Agt)
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="lineupTabContent">
        <!-- Day 1 Panel -->
        <div class="tab-pane fade show active" id="day1-pane" role="tabpanel" aria-labelledby="day1-tab" tabindex="0">
            <div class="row g-4">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/736x/0c/f8/97/0cf8976e6af05f582576c7d0aeef84c3.jpg" class="img-fluid rounded" alt="">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Taylor Swift</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">Pop</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/1200x/fb/2c/29/fb2c292c50595fd5a2ba4b0b65300f29.jpg" class="img-fluid rounded" alt="">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Justin Bieber</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">POP</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/1200x/19/b3/95/19b395e4e347a49626283b85ffbf9ce4.jpg" class="img-fluid rounded" alt="">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Ariana Grande</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">POP</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/736x/39/3a/73/393a73e84529335efd222ead358c8662.jpg" class="img-fluid rounded" alt="">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Bruno Mars</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">Pop / Soul</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Day 2 Panel -->
        <div class="tab-pane fade" id="day2-pane" role="tabpanel" aria-labelledby="day2-tab" tabindex="0">
            <div class="row g-4">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/736x/d2/b2/1f/d2b21f270f752d5932e1cd484459b980.jpg" class="img-fluid rounded" alt="">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Charlie Puth</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">Pop</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/736x/96/05/5a/96055aa0294874cc1f3cdcc67f29f547.jpg" class="img-fluid rounded" style="object-position: center 15%;" alt="Olivia Rodrigo">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Olivia Rodrigo</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">Pop</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/1200x/ce/ff/6f/ceff6fdfd08a7d293587210a8dd3c5b3.jpg" class="img-fluid rounded" style="object-position: center 10%;" alt="NIKI">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">NIKI</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">R&B / Pop</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="artist-card">
                        <img src="https://i.pinimg.com/736x/73/9a/09/739a09b8df187c87b6df5e5416d101fa.jpg" class="img-fluid rounded" style="object-position: center 15%;" alt="Rex Orange County">
                        <div class="artist-info">
                            <h5 class="fw-bold mb-0">Rex Orange County</h5>
                            <span class="text-white-50" style="font-size: 0.8rem;">Indie Pop</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Tickets Section -->
<section class="container my-5 py-5 no-print" id="kategori-tiket">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-slate-900"><i class="fa-solid fa-tags text-indigo me-2"></i>Kategori Tiket</h2>
        <p class="text-muted">Pilih paket tiket sesuai kebutuhan festival musik Anda</p>
    </div>
    
    <!-- Tab Navigation for Pricing -->
    <div class="d-flex justify-content-center mb-5">
        <ul class="nav nav-pills gap-3" id="pricingTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link nav-link-custom active" id="single-pass-tab" data-bs-toggle="tab" data-bs-target="#single-pass-pane" type="button" role="tab" aria-controls="single-pass-pane" aria-selected="true">
                    <i class="fa-solid fa-calendar-day me-2"></i>Harian (Day 1 / Day 2)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link nav-link-custom" id="two-pass-tab" data-bs-toggle="tab" data-bs-target="#two-pass-pane" type="button" role="tab" aria-controls="two-pass-pane" aria-selected="false">
                    <i class="fa-solid fa-calendar-days me-2"></i>Terusan (2-Day Pass)
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="pricingTabContent">
        
        <!-- Single-Day Pass Pane -->
        <div class="tab-pane fade show active" id="single-pass-pane" role="tabpanel" aria-labelledby="single-pass-tab" tabindex="0">
            <div class="row g-4 align-items-stretch justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <div class="pricing-card p-4">
                        <div class="mb-4">
                            <span class="badge badge-category badge-reguler mb-2">Reguler (Harian)</span>
                            <h3 class="fw-bold mb-2">Standard Access</h3>
                            <p class="text-muted" style="font-size: 0.9rem;">Akses masuk area festival utama selama 1 hari pilihan Anda.</p>
                        </div>
                        <div class="mb-4 mt-auto">
                            <h2 class="fw-bold mb-0 text-slate-800">Rp 7.500.000</h2>
                            <span class="text-muted" style="font-size: 0.8rem;">per tiket / hari</span>
                        </div>
                        <ul class="list-unstyled mb-4 text-slate-600" style="font-size: 0.9rem; line-height: 1.8;">
                            <li><i class="fa-solid fa-check text-success me-2"></i> Akses masuk area festival 1 hari pilihan</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Akses ke area panggung utama (di belakang barikade VIP)</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Akses tenant F&B dan instalasi seni</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Gelang RFID standar 1 hari</li>
                        </ul>
                        <div class="d-grid gap-2">
                            <a href="create.php?kategori=Reguler&hari=Day 1" class="btn btn-outline-indigo py-2 fw-semibold">Pesan Day 1</a>
                            <a href="create.php?kategori=Reguler&hari=Day 2" class="btn btn-outline-indigo py-2 fw-semibold">Pesan Day 2</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-5 col-lg-4">
                    <div class="pricing-card featured p-4">
                        <div class="mb-4">
                            <span class="badge badge-category badge-vip mb-2">VIP (Harian)</span>
                            <h3 class="fw-bold mb-2">Front Row Access</h3>
                            <p class="text-muted" style="font-size: 0.9rem;">Area menonton terbaik di barisan depan selama 1 hari.</p>
                        </div>
                        <div class="mb-4 mt-auto">
                            <h2 class="fw-bold mb-0 text-indigo">Rp 16.500.000</h2>
                            <span class="text-muted" style="font-size: 0.8rem;">per tiket / hari</span>
                        </div>
                        <ul class="list-unstyled mb-4 text-slate-600" style="font-size: 0.9rem; line-height: 1.8;">
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Area menonton khusus barisan depan (Front Row Section)</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Jalur antrean masuk khusus VIP (Fast Track Entry)</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Akses ke VIP Lounge dan Toilet ber-AC</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Gratis 1x penukaran makanan & minuman di VIP Lounge</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Gelang RFID VIP 1 Hari</li>
                        </ul>
                        <div class="d-grid gap-2">
                            <a href="create.php?kategori=VIP&hari=Day 1" class="btn btn-premium-primary py-2 fw-semibold">Pesan Day 1</a>
                            <a href="create.php?kategori=VIP&hari=Day 2" class="btn btn-premium-primary py-2 fw-semibold">Pesan Day 2</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 2-Day Pass Pane -->
        <div class="tab-pane fade" id="two-pass-pane" role="tabpanel" aria-labelledby="two-pass-tab" tabindex="0">
            <div class="row g-4 align-items-stretch justify-content-center">
                <div class="col-md-4">
                    <div class="pricing-card p-4">
                        <div class="mb-4">
                            <span class="badge badge-category badge-reguler mb-2">Reguler (2-Day)</span>
                            <h3 class="fw-bold mb-2">2-Day Value Pass</h3>
                            <p class="text-muted" style="font-size: 0.9rem;">Akses standar penuh selama 2 hari festival.</p>
                        </div>
                        <div class="mb-4 mt-auto">
                            <h2 class="fw-bold mb-0 text-slate-800">Rp 14.000.000</h2>
                            <span class="text-muted" style="font-size: 0.8rem;">hemat Rp 1.000.000!</span>
                        </div>
                        <ul class="list-unstyled mb-4 text-slate-600" style="font-size: 0.9rem; line-height: 1.8;">
                            <li><i class="fa-solid fa-check text-success me-2"></i> Akses masuk area festival penuh 2 hari (Day 1 & Day 2)</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Gelang RFID kain khusus edisi 2 Hari (tanpa antre cetak hari ke-2)</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Akses penuh ke area tenant F&B dan instalasi seni</li>
                        </ul>
                        <a href="create.php?kategori=Reguler&hari=2-Day Pass" class="btn btn-outline-indigo w-100 py-2 fw-semibold mt-auto">Pesan Terusan 2 Hari</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="pricing-card featured p-4">
                        <div class="mb-4">
                            <span class="badge badge-category badge-vip mb-2">VIP (2-Day)</span>
                            <h3 class="fw-bold mb-2">Ultimate Festival</h3>
                            <p class="text-muted" style="font-size: 0.9rem;">Fasilitas VIP penuh selama 2 hari festival.</p>
                        </div>
                        <div class="mb-4 mt-auto">
                            <h2 class="fw-bold mb-0 text-indigo">Rp 32.500.000</h2>
                        </div>
                        <ul class="list-unstyled mb-4 text-slate-600" style="font-size: 0.9rem; line-height: 1.8;">
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Area menonton Front Row Section selama 2 hari berturut-turut</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Jalur antrean masuk khusus VIP di kedua hari</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Akses penuh ke VIP Lounge dan Toilet ber-AC</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Voucher makanan dan minuman harian</li>
                            <li><i class="fa-solid fa-check text-indigo me-2"></i> Paket Merchandise Eksklusif (T-shirt, lanyard, & tote bag)</li>
                        </ul>
                        <a href="create.php?kategori=VIP&hari=2-Day Pass" class="btn btn-premium-primary w-100 py-2 fw-semibold mt-auto">Pesan Terusan 2 Hari</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="pricing-card p-4">
                        <div class="mb-4">
                            <span class="badge badge-category badge-vvip mb-2">VVIP (2-Day)</span>
                            <h3 class="fw-bold mb-2">Backstage Pass</h3>
                            <p class="text-muted" style="font-size: 0.9rem;">Akses belakang panggung dan fasilitas lounge premium.</p>
                        </div>
                        <div class="mb-4 mt-auto">
                            <h2 class="fw-bold mb-0 text-slate-800">Rp 85.000.000</h2>
                            <span class="text-muted" style="font-size: 0.8rem;">Exclusive 2-Day Access</span>
                        </div>
                        <ul class="list-unstyled mb-4 text-slate-600" style="font-size: 0.9rem; line-height: 1.8;">
                            <li><i class="fa-solid fa-check text-success me-2"></i> Semua keuntungan VIP 2-Day</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Akses VVIP Suite selama 2 hari penuh</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Backstage Tour 2 Hari</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Meet & Greet / Photo Group bersama artis pilihan</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Super Exclusive Box (Signed by performers)</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Layanan Dedicated Concierge (Personal Assistant)</li>
                        </ul>
                        <a href="create.php?kategori=VVIP&hari=2-Day Pass" class="btn btn-outline-indigo w-100 py-2 fw-semibold mt-auto">Pesan Terusan 2 Hari</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<!-- Lookup & Virtual Ticket Section -->
<section class="container my-5 py-5" id="cek-tiket">
    <div class="row justify-content-center">
        <div class="col-lg-6 no-print">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-slate-900"><i class="fa-solid fa-magnifying-glass text-indigo me-2"></i>Cek Status Tiket Anda</h2>
                <p class="text-muted">Masukkan alamat email pendaftaran Anda untuk mencetak atau melihat E-Ticket</p>
            </div>
            
            <form action="index.php#cek-tiket" method="GET" class="mb-5">
                <div class="input-group">
                    <input type="email" name="email_search" class="form-control" placeholder="Contoh: rian@example.com" value="<?php echo htmlspecialchars($search_email); ?>" required>
                    <button class="btn btn-premium-primary" type="submit">Cari Tiket</button>
                </div>
            </form>
            
            <!-- Alert Session Notification -->
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

            <?php if ($searched && !$ticket_found): ?>
                <div class="alert alert-danger alert-aidfest d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
                    <div>Alamat email "<strong><?php echo htmlspecialchars($search_email); ?></strong>" belum terdaftar. Silakan lakukan pemesanan terlebih dahulu.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Render Virtual Ticket if Found -->
    <?php if ($searched && $ticket_found): ?>
        <div class="row justify-content-center" id="section-ticket-render">
            <div class="col-lg-6 text-center mb-3 no-print">
                <?php if ($ticket_found['status_pembayaran'] === 'Lunas'): ?>
                    <div class="alert alert-success alert-aidfest d-inline-flex align-items-center" role="alert">
                        <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                        <div>Tiket Lunas! E-Ticket virtual Anda aktif dan siap dicetak.</div>
                    </div>
                <?php elseif ($ticket_found['status_pembayaran'] === 'Pending'): ?>
                    <div class="alert alert-warning alert-aidfest d-inline-flex align-items-center" role="alert">
                        <i class="fa-solid fa-clock fs-5 me-2"></i>
                        <div>Tiket Pending! Harap selesaikan pembayaran untuk mengaktifkan cetak tiket.</div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger alert-aidfest d-inline-flex align-items-center" role="alert">
                        <i class="fa-solid fa-circle-xmark fs-5 me-2"></i>
                        <div>Tiket Dibatalkan! Tiket ini tidak lagi berlaku untuk masuk.</div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-12">
                <!-- Virtual Ticket Card -->
                <div class="ticket-virtual" id="e-ticket-card">
                    <div class="ticket-header">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fa-solid fa-compact-disc text-white me-2 fs-5"></i>
                            <h5 class="fw-bold mb-0 tracking-widest text-uppercase">E-Ticket AidFest 2026</h5>
                        </div>
                        <span class="text-white-50" style="font-size: 0.8rem;">Pintu Masuk Utama Utama • Jakarta, Indonesia</span>
                    </div>
                    <div class="ticket-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-8">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nama Pemesan</span>
                                <span class="fw-bold fs-5 text-slate-800"><?php echo htmlspecialchars($ticket_found['nama_pemesan']); ?></span>
                            </div>
                            <div class="col-4 text-end">
                                <?php 
                                $badgeClass = '';
                                if ($ticket_found['kategori_tiket'] == 'Reguler') $badgeClass = 'badge-reguler';
                                elseif ($ticket_found['kategori_tiket'] == 'VIP') $badgeClass = 'badge-vip';
                                elseif ($ticket_found['kategori_tiket'] == 'VVIP') $badgeClass = 'badge-vvip';
                                ?>
                                <span class="badge badge-category <?php echo $badgeClass; ?>"><?php echo $ticket_found['kategori_tiket']; ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Alamat Email</span>
                            <span class="fw-semibold text-slate-700"><?php echo htmlspecialchars($ticket_found['email']); ?></span>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tanggal Pesan</span>
                                <span class="text-slate-600" style="font-size: 0.85rem;">
                                    <?php echo date('d M Y - H:i', strtotime($ticket_found['tanggal_pesan'])); ?> WIB
                                </span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nomor Tiket</span>
                                <span class="fw-bold text-indigo">#ADF-<?php echo str_pad($ticket_found['id_tiket'], 5, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pilihan Hari</span>
                                <span class="text-slate-700 fw-bold"><i class="fa-solid fa-calendar-day text-muted me-1"></i><?php echo htmlspecialchars($ticket_found['paket_hari']); ?></span>
                            </div>
                            <div class="col-6 text-end">
                                <!-- empty space -->
                            </div>
                        </div>
                        
                        <!-- Rincian Status Pembayaran Pembeli -->
                        <div class="row align-items-center mb-4">
                            <div class="col-6">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Status Bayar</span>
                                <?php if ($ticket_found['status_pembayaran'] === 'Lunas'): ?>
                                    <span class="text-success fw-bold" style="font-size: 0.95rem;"><i class="fa-solid fa-circle-check me-1"></i>LUNAS</span>
                                <?php elseif ($ticket_found['status_pembayaran'] === 'Pending'): ?>
                                    <span class="text-warning fw-bold" style="font-size: 0.95rem;"><i class="fa-solid fa-clock me-1"></i>PENDING</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold" style="font-size: 0.95rem;"><i class="fa-solid fa-circle-xmark me-1"></i>BATAL</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Metode Pembayaran</span>
                                <span class="text-slate-700 fw-semibold" style="font-size: 0.85rem;">
                                    <?php echo $ticket_found['status_pembayaran'] === 'Lunas' ? htmlspecialchars($ticket_found['metode_pembayaran']) : 'Belum Membayar'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="ticket-divider"></div>
                        
                        <div class="text-center pt-2">
                            <?php if ($ticket_found['status_pembayaran'] === 'Lunas'): ?>
                                <?php 
                                $ticket_code = 'ADF-' . str_pad($ticket_found['id_tiket'], 5, '0', STR_PAD_LEFT);
                                $qr_data = "TICKET_ID: " . $ticket_code . "\n"
                                         . "CODE: " . $ticket_found['kode_pembayaran'] . "\n"
                                         . "NAME: " . $ticket_found['nama_pemesan'] . "\n"
                                         . "CATEGORY: " . $ticket_found['kategori_tiket'] . "\n"
                                         . "DAY: " . $ticket_found['paket_hari'];
                                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
                                ?>
                                <div class="mb-3 d-inline-block p-2 bg-white border rounded-3 shadow-sm">
                                    <img src="<?php echo $qr_url; ?>" alt="QR Code E-Ticket" class="img-fluid" style="width: 130px; height: 130px; display: block;">
                                </div>
                                <div class="fw-bold text-slate-800 mb-1" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                                    KODE TIKET: <?php echo htmlspecialchars($ticket_found['kode_pembayaran']); ?>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Scan QR Code di atas saat memasuki gerbang festival musik.</p>
                            <?php elseif ($ticket_found['status_pembayaran'] === 'Pending'): ?>
                                <div class="alert alert-warning py-2 mb-0" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-lock me-1"></i> QR Code dikunci hingga pembayaran diselesaikan.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger py-2 mb-0" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-ban me-1"></i> Tiket ini hangus karena pembatalan transaksi.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 text-center mt-3 mb-5 no-print">
                <?php if ($ticket_found['status_pembayaran'] === 'Lunas'): ?>
                    <button onclick="downloadTicketPDF();" class="btn btn-premium-primary px-4 py-2">
                        <i class="fa-solid fa-download me-2"></i>Unduh E-Ticket (PDF)
                    </button>
                <?php elseif ($ticket_found['status_pembayaran'] === 'Pending'): ?>
                    <a href="payment.php?id=<?php echo $ticket_found['id_tiket']; ?>" class="btn btn-warning px-4 py-2 fw-semibold">
                        <i class="fa-solid fa-credit-card me-2"></i>Bayar Tiket Sekarang
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary px-4 py-2" disabled>
                        <i class="fa-solid fa-ban me-2"></i>Cetak Dinonaktifkan
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- CDN html2pdf.js & Script Konversi PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadTicketPDF() {
    const element = document.getElementById('e-ticket-card');
    
    // Hilangkan box shadow sementara agar PDF bersih dari bayangan abu-abu terpotong
    const originalShadow = element.style.boxShadow;
    element.style.boxShadow = 'none';

    const opt = {
        margin:       [0.15, 0.15, 0.15, 0.15],
        filename:     'AidFest2026-ETicket-<?php echo isset($ticket_found) ? str_pad($ticket_found['id_tiket'], 5, '0', STR_PAD_LEFT) : "00000"; ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { 
            scale: 2, 
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            backgroundColor: '#ffffff'
        },
        jsPDF:        { unit: 'in', format: 'a5', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save().then(() => {
        // Kembalikan box-shadow setelah proses unduh selesai
        element.style.boxShadow = originalShadow;
    });
}
</script>

<?php if (isset($_SESSION['send_email_id'])): ?>
<!-- Pemicu Asinkron Kirim Email E-Ticket di Background -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('send_email_async.php?id=<?php echo $_SESSION['send_email_id']; ?>')
        .then(response => response.json())
        .then(data => {
            console.log('Async Email Dispatch:', data);
        })
        .catch(err => {
            console.error('Async Email Error:', err);
        });
});
</script>
<?php unset($_SESSION['send_email_id']); ?>
<?php endif; ?>

<?php
// Load Footer Global
include_once 'includes/footer.php';
?>
