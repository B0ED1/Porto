    <footer class="mt-auto py-5 bg-slate-900 border-top border-white-5 no-print">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3 cursor-pointer" id="footer-brand-logo" style="user-select: none;">
                        <i class="fa-solid fa-compact-disc text-info me-2 fs-4"></i>
                        <span class="fw-bold text-white fs-4" style="font-family: 'Outfit', sans-serif;">AidFest</span>
                    </div>
                    <p class="text-white-50 small mb-4" style="line-height: 1.6;">
                        Rasakan getaran musik festival terbesar tahun ini. Tampil memukau dengan jajaran artis internasional terpopuler di panggung utama Jakarta.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="https://www.instagram.com/budipanggill" target="_blank" rel="noopener noreferrer" class="text-white-50 hover-white fs-5" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@yearning24h" target="_blank" rel="noopener noreferrer" class="text-white-50 hover-white fs-5" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <h6 class="text-white fw-bold mb-3 text-uppercase tracking-wider" style="font-size: 0.85rem;">Tautan Pintas</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li><a href="<?php echo $base_path; ?>index.php" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-2" style="font-size: 0.75rem;"></i>Beranda</a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#lineup" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-2" style="font-size: 0.75rem;"></i>Artist Lineup</a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#kategori-tiket" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-2" style="font-size: 0.75rem;"></i>Kategori Tiket</a></li>
                        <li><a href="<?php echo $base_path; ?>index.php#cek-tiket" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-2" style="font-size: 0.75rem;"></i>Cek E-Ticket</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-12">
                    <h6 class="text-white fw-bold mb-3 text-uppercase tracking-wider" style="font-size: 0.85rem;">Informasi Festival</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-white-50">
                        <li class="d-flex align-items-center"><i class="fa-solid fa-calendar-days text-info me-3"></i>15 - 16 Agustus 2026</li>
                        <li class="d-flex align-items-center"><i class="fa-solid fa-location-dot text-info me-3"></i>Gelora Bung Karno, Jakarta</li>
                        <li class="d-flex align-items-center"><i class="fa-solid fa-envelope text-info me-3"></i>support@aidfest.com</li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-white-10 my-4">
            
            <div class="row align-items-center justify-content-between flex-column flex-md-row gap-2 small">
                <div class="col-auto text-white-50">
                    © <?php echo date('Y'); ?> <strong class="text-white">AidFest</strong>. All rights reserved.
                </div>
                <div class="col-auto text-white-50">
                    Keamanan Transaksi Terjamin • E-Ticket Resmi
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle CDN (Includes Popper for dropdowns & tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Client-Side JS -->
    <script src="<?php echo $base_path; ?>assets/js/main.js"></script>
</body>
</html>
