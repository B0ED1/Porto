/**
 * AidFest - Main Client-Side Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // Konfirmasi Hapus Data
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const deleteUrl = this.getAttribute('href');
            const pemesanName = this.getAttribute('data-name') || 'pemesan ini';
            
            if (confirm(`Apakah Anda yakin ingin menghapus tiket atas nama "${pemesanName}" secara permanen? Action ini tidak dapat dibatalkan.`)) {
                window.location.href = deleteUrl;
            }
        });
    });

    // Validasi Form
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            const nameInput = form.querySelector('input[name="nama_pemesan"]');
            let customValid = true;

            if (nameInput && nameInput.value.trim().length < 3) {
                nameInput.setCustomValidity('Nama minimal harus 3 karakter.');
                customValid = false;
            } else if (nameInput) {
                nameInput.setCustomValidity('');
            }

            if (!form.checkValidity() || !customValid) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });

    // Efek Focus Input
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', () => {
            if (input.value === '') {
                input.parentElement.classList.remove('focused');
            }
        });
    });

    // Auto-fade alert banners
    const alerts = document.querySelectorAll('.alert-aidfest');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 600);
        }, 5000);
    });

    // Sinkronisasi Kategori VVIP & Pilihan Hari
    const kategoriSelect = document.getElementById('kategori_tiket');
    const paketHariSelect = document.getElementById('paket_hari');
    if (kategoriSelect && paketHariSelect) {
        const updatePackageOptions = () => {
            if (kategoriSelect.value === 'VVIP') {
                paketHariSelect.value = '2-Day Pass';
                Array.from(paketHariSelect.options).forEach(option => {
                    if (option.value !== '2-Day Pass') {
                        option.disabled = true;
                    }
                });
            } else {
                Array.from(paketHariSelect.options).forEach(option => {
                    option.disabled = false;
                });
            }
        };
        kategoriSelect.addEventListener('change', updatePackageOptions);
        updatePackageOptions();
    }

    // Easter Egg: Double-click logo brand di footer untuk login admin
    const footerLogo = document.getElementById('footer-brand-logo');
    if (footerLogo) {
        footerLogo.addEventListener('dblclick', () => {
            const isAdminPath = window.location.pathname.includes('/admin/');
            if (isAdminPath) {
                window.location.href = 'login.php';
            } else {
                window.location.href = 'admin/login.php';
            }
        });
    }

    // Shortcut Keyboard: Ctrl + Shift + A untuk login admin
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'a') {
            e.preventDefault();
            const isAdminPath = window.location.pathname.includes('/admin/');
            if (isAdminPath) {
                window.location.href = 'login.php';
            } else {
                window.location.href = 'admin/login.php';
            }
        }
    });
});
