# Dokumentasi Proyek Akhir: AidFest 2026 (MusicFest CRUD)

Dokumentasi ini disusun untuk mempermudah pemahaman, instalasi, pengujian, serta bahan presentasi Ujian Akhir Semester (UAS) Praktikum Pemrograman Web.

---

## 1. Deskripsi Proyek
**AidFest 2026** adalah aplikasi web manajemen pendaftaran dan penjualan tiket festival musik internasional berbasis **Native PHP** dan **MySQL/PostgreSQL (PDO)**. Aplikasi ini dirancang untuk menangani alur pendaftaran pengunjung (*Create*), verifikasi status tiket (*Read*), penyesuaian detail tiket oleh admin (*Update*), serta pembatalan pendaftaran (*Delete*), sehingga memenuhi kriteria fungsionalitas CRUD secara penuh dan responsif.

### Target Pengguna:
1. **Pembeli Tiket (Client-Side):** Melakukan pemesanan tiket secara mandiri, memilih kategori dan paket hari, menyelesaikan checkout pembayaran simulasi, serta mencari dan mencetak E-Ticket virtual.
2. **Administrator (Admin Panel):** Mengelola seluruh pendaftaran tiket festival, memantau metrik pendaftaran secara langsung (real-time stats & revenue), menyunting detail status pemesan, serta menghapus pendaftaran yang tidak valid.

---

## 2. Jajaran Teknologi (Tech Stack)
* **Backend:** Native PHP (dengan PDO untuk perlindungan terhadap SQL Injection).
* **Database:** MySQL (Lokal XAMPP) / PostgreSQL (Dukungan database cloud Supabase).
* **Frontend:** HTML5, CSS3 (Vanilla dengan visual premium), Bootstrap 5 (CSS Framework), FontAwesome 6 (Ikon), Outfit (Google Fonts).
* **Fitur Interaktif:** JavaScript (Vanilla JS untuk validasi form real-time dan logika sinkronisasi).

---

## 3. Struktur Database
Database default bernama `db_aidfest` memiliki dua tabel utama dengan relasi operasional:

### A. Tabel `admin` (Autentikasi Pengelola)
Tabel ini digunakan untuk menyimpan data administrator agar panel admin terlindungi.
```sql
CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
```

### B. Tabel `pendaftaran_tiket` (Data Transaksi Tiket)
Tabel ini merekam semua informasi pesanan, status pembayaran, dan rincian metode bayar.
```sql
CREATE TABLE pendaftaran_tiket (
    id_tiket INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    kategori_tiket ENUM('Reguler', 'VIP', 'VVIP') NOT NULL,
    paket_hari ENUM('Day 1', 'Day 2', '2-Day Pass') DEFAULT '2-Day Pass',
    status_pembayaran ENUM('Pending', 'Lunas', 'Batal') DEFAULT 'Pending',
    metode_pembayaran VARCHAR(50) DEFAULT NULL,
    kode_pembayaran VARCHAR(50) DEFAULT NULL,
    tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## 4. Struktur File Proyek
Aplikasi diorganisasikan secara modular untuk mempermudah pemeliharaan kode:
```text
Webkali/
│
├── config/
│   └── database.php         # Inisialisasi PDO, seeding database awal & migrasi otomatis
│
├── database/
│   └── schema.sql           # File dump SQL untuk impor database manual
│
├── includes/
│   ├── functions.php        # Fungsi helper terpusat (tarif harga, CRUD db, format indo)
│   ├── header.php           # Navbar modern dengan glassmorphism & status sesi login
│   └── footer.php           # Kaki halaman dan pemanggilan JS global
│
├── assets/
│   ├── css/
│   │   └── style.css        # Desain visual premium (gradasi, stat-cards, ticket-design)
│   └── js/
│       └── main.js          # JS validasi, alert auto-fade, dan sinkronisasi VVIP
│
├── admin/
│   ├── login.php            # Halaman login administrator
│   ├── logout.php           # Proses logout & penghancuran session admin
│   ├── index.php            # Dashboard utama admin (statistik visual & tabel CRUD)
│   ├── edit.php             # Form penyuntingan data tiket pembeli
│   └── delete.php           # Skrip pemrosesan hapus data tiket
│
├── index.php                # Portal pembeli (Hero, lineup artis, tarif, pencarian tiket)
├── create.php               # Formulir pemesanan tiket untuk pembeli / admin
└── payment.php              # Halaman simulasi checkout pembayaran tiket
```

---

## 5. Fitur-Fitur Utama Proyek (Bahan Presentasi)
Saat mempresentasikan aplikasi ini di depan penguji/dosen, berikut adalah poin penting yang dapat ditonjolkan:

1. **Sentralisasi Logika Harga (*Single Source of Truth*):**
   Tarif harga tiket didefinisikan secara terpusat pada file [functions.php](file:///c:/xampp/htdocs/Webkali/includes/functions.php) di fungsi `get_ticket_price()`. Perubahan tarif akan otomatis mengubah tampilan di landing page dan nominal tagihan di halaman checkout pembayaran secara sinkron.
2. **Dashboard Statistik & Revenue Terintegrasi:**
   Panel admin menampilkan kartu ringkasan data yang dihitung langsung dari database secara dinamis:
   * Total Pendaftar keseluruhan.
   * **Total Pendapatan Bersih** (dihitung hanya dari pesanan yang berstatus **Lunas**).
   * Jumlah pendaftar per kategori tiket (Reguler, VIP, VVIP).
3. **Logika Sinkronisasi Form VVIP (Dynamic Form UX):**
   Sesuai aturan bisnis, tiket VVIP hanya tersedia dalam bentuk *2-Day Pass* (Terusan). Di halaman pemesanan, JavaScript secara dinamis mendeteksi pilihan kategori. Jika VVIP dipilih, opsi harian (Day 1 / Day 2) akan langsung dinonaktifkan di frontend untuk mencegah kesalahan input pengguna sebelum diserahkan ke backend.
4. **Unduh E-Ticket PDF Instan (Client-Side PDF Generation):**
   E-Ticket virtual kini dapat langsung diunduh dalam bentuk file PDF secara otomatis menggunakan library `html2pdf.js` saat tombol **"Unduh E-Ticket (PDF)"** diklik. PDF dirancang dengan layout proporsional berukuran A5, menonaktifkan efek bayangan (*box shadow*) sementara saat proses konversi agar file bersih dari bayangan terpotong, dan siap dicetak atau disimpan oleh pembeli.
5. **Akses Login Admin Tersembunyi (Easter Egg & Shortcut Security):**
   Untuk menjaga keamanan, tombol "Login Admin" sengaja dihilangkan dari menu navigasi utama agar tidak diklik oleh pengunjung biasa. Admin dapat mengakses halaman login dengan 2 cara rahasia:
   * **Double-click (Klik 2 Kali)** pada teks logo **"AidFest"** di bagian **Footer** (bukan di navbar, karena link navbar memicu reload halaman secara otomatis).
   * Menekan kombinasi tombol keyboard **`Ctrl + Shift + A`** secara bersamaan dari halaman mana pun di situs web. Logika jalur dinamis JavaScript memastikan pengalihan berjalan mulus di hosting mana pun.
6. **Simulasi Gerbang Pembayaran Realistis (Payment Gateway UI):**
   Layar pembayaran di [payment.php](file:///c:/xampp/htdocs/Webkali/payment.php) dirancang mirip dengan alur pembayaran asli (seperti Midtrans/Xendit). Menampilkan timer hitung mundur (10 menit), QRIS QR Code dinamis jika memilih e-wallet (GoPay/DANA), atau nomor Virtual Account dengan tombol salin cepat untuk transfer bank. Tombol "Konfirmasi Pembayaran" memicu simulasi verifikasi status (spinner loader bank) sebelum akhirnya mengubah status menjadi Lunas.
7. **Pengiriman Email E-Ticket Asinkron (Asynchronous Background Mail Dispatch):**
   Untuk mencegah halaman checkout mengalami loading lama (yang disebabkan oleh latensi koneksi SMTP Gmail jika dikirim secara sinkron), sistem kini menggunakan pendekatan asinkron. Setelah status pembayaran diubah menjadi *Lunas*, browser akan langsung dialihkan ke halaman E-Ticket **seketika** (<0.5 detik). Halaman tiket kemudian memicu pemanggilan skrip background [send_email_async.php](file:///c:/xampp/htdocs/Webkali/send_email_async.php) menggunakan API JavaScript `fetch()`. Pendekatan ini menjamin pengalaman pengguna (UX) yang sangat cepat dan responsif di server hosting mana pun tanpa terpengaruh oleh latensi server SMTP pengirim.

---

## 6. Panduan Instalasi (Local Setup)

1. **Siapkan Folder Proyek:**
   Salin folder `Webkali` ke direktori root server lokal Anda (misal `C:\xampp\htdocs\` untuk XAMPP).
2. **Import Database:**
   * Aktifkan Apache dan MySQL di XAMPP Control Panel.
   * Buka browser dan akses `http://localhost/phpmyadmin/`.
   * Buat database baru bernama `db_aidfest`.
   * Pilih menu **Import**, lalu pilih file `database/schema.sql` dan klik **Go**.
3. **Konfigurasi database.php:**
   * Buka file [config/database.php](file:///c:/xampp/htdocs/Webkali/config/database.php).
   * Pada baris ke-7, pastikan driver diatur ke `'mysql'` untuk database lokal:
     ```php
     $db_driver = 'mysql';
     ```
4. **Jalankan Aplikasi:**
   * Buka browser dan akses alamat: `http://localhost/Webkali/index.php`.

---

## 7. Kredensial Akses Admin (Default)
Untuk login ke halaman administrator (`http://localhost/Webkali/admin/login.php`):
* **Username:** `admin`
* **Password:** `password123`

---

## 8. Panduan Alur Presentasi Demo Aplikasi (Tips UAS)

1. **Perkenalan:** Jelaskan nama proyek (AidFest 2026) dan latar belakang pembuatannya sebagai aplikasi berbasis PHP Native yang responsif.
2. **Demo Sisi Pembeli:** 
   * Tunjukkan halaman beranda, lineup artis global, dan tabel kategori tiket dengan tarif harga terbaru yang premium.
   * Lakukan pemesanan tiket baru di menu **Pesan Tiket**. Tunjukkan interaksi ketika memilih **VVIP** yang secara otomatis mengunci hari ke **2-Day Pass**.
   * Isi form dengan nama dan email baru, kemudian submit.
   * Anda akan masuk ke halaman **Checkout Pembayaran**. Pilih metode pembayaran, lalu selesaikan transaksi dengan menekan **Bayar Sekarang**.
   * Tunjukkan bahwa E-Ticket virtual langsung aktif dengan status **Lunas**, lengkap dengan kode transaksi acak, QR Code dinamis untuk scan masuk, dan tombol cetak.
3. **Demo Sisi Administrator:**
   * Jelaskan bahwa link login sengaja disembunyikan dari navbar untuk meningkatkan keamanan web.
   * Lakukan **Double-Click (Klik 2 Kali)** pada logo brand **"AidFest"** di bagian **Footer** (atau gunakan shortcut **`Ctrl + Shift + A`**) untuk membuka halaman login admin secara rahasia!
   * Masuk menggunakan username `admin` dan password `password123`.
   * Tunjukkan visualisasi **Statistik Pendaftar** & **Total Pendapatan** yang langsung bertambah setelah pendaftaran baru berhasil diselesaikan di langkah sebelumnya.
   * Lakukan pengujian CRUD: coba edit salah satu data tiket pendaftar (misal mengubah status pembayaran atau kategori) dan hapus satu data tiket pendaftar menggunakan tombol aksi interaktif.
   * Tunjukkan fungsionalitas fitur **Pencarian Data** yang dapat menyaring data di tabel admin secara instan.
4. **Penutup:** Jelaskan mengenai kebersihan struktur kode (*clean code*) dengan dokumentasi ini.

---

## 8. Panduan Membuat Email Bisnis Custom Domain (`no-reply@domainanda.com`)

Jika Anda ingin E-Ticket dikirim menggunakan email profesional dengan nama domain sendiri (seperti `no-reply@aidfest.com`), berikut adalah langkah-langkah lengkap untuk membuat akun email tersebut di hosting dan mengaturnya di sistem website:

### Langkah 1: Buat Akun Email Baru di Hosting (Hostinger / cPanel)
1. Masuk ke **hPanel Hostinger** atau **cPanel** hosting Anda.
2. Cari menu **Email Accounts** (Akun Email) atau **Emails**.
3. Klik tombol **Create** (Buat) atau **Add Email Account**.
4. Isi data sebagai berikut:
   * **Email Address / Username:** `no-reply`
   * **Domain:** Pilih nama domain Anda (misal `aidfest.com` atau domain aktif milik Anda).
   * **Password:** Buat kata sandi baru yang kuat.
5. Klik tombol **Create / Buat**.

### Langkah 2: Dapatkan Informasi Host & Port SMTP Hosting
1. Setelah akun email berhasil dibuat, klik tombol **Connect Devices** atau **Set Up Mail Client** di samping nama email tersebut.
2. Cari dan catat detail informasi **SMTP Manual Configuration** (Pengaturan SMTP Manual). Informasi yang Anda butuhkan biasanya berupa:
   * **SMTP Host / Server:** (contoh: `smtp.hostinger.com` atau `mail.domainanda.com`)
   * **SMTP Port:** `465` (untuk enkripsi SSL) atau `587` (untuk enkripsi TLS)
   * **SSL/TLS Mode:** `ssl` atau `tls`

### Langkah 3: Konfigurasi File Program di Website
Buka berkas konfigurasi **[config/mail.php](file:///c:/xampp/htdocs/Webkali/config/mail.php)** di web AidFest, lalu sesuaikan isinya dengan detail SMTP email bisnis Anda yang baru:

```php
return [
    'mail_enabled' => true, 

    // Konfigurasi Server SMTP Hosting Baru Anda
    'smtp_host'     => 'smtp.hostinger.com',       // Ganti dengan SMTP Host Anda
    'smtp_port'     => 465,                        // Ganti ke 465 (SSL) atau 587 (TLS)
    'smtp_secure'   => 'ssl',                      // Ganti ke 'ssl' atau 'tls'
    'smtp_user'     => 'no-reply@domainanda.com',  // Email bisnis baru Anda
    'smtp_pass'     => 'PASSWORD_EMAIL_BISNIS_ANDA', // Password email bisnis Anda

    // Pengaturan Pengirim
    'from_email'    => 'no-reply@domainanda.com',  // Samakan dengan smtp_user Anda
    'from_name'     => 'AidFest 2026 E-Ticket Support',
];
```

### Langkah 4: Tambahkan DNS Record untuk Mencegah Email Masuk Folder Spam (Penting!)
Agar email yang dikirim dari domain bisnis Anda tidak dicurigai sebagai spam/phishing oleh Yahoo, Gmail, dll., tambahkan **DNS Record** berikut pada domain manager Anda (misalnya di Cloudflare, Hostinger DNS, atau cPanel DNS Zone Editor):

1. **SPF Record (TXT):**
   * **Type:** `TXT`
   * **Name / Host:** `@`
   * **Value:** `v=spf1 include:_spf.mail.hostinger.com ~all` (Sesuaikan bagian `include` dengan anjuran provider hosting email Anda).
2. **DKIM Record (TXT):**
   * Cari menu **DKIM** di email hosting Anda untuk mendapatkan kode kunci unik.
   * **Type:** `TXT`
   * **Name / Host:** `default._domainkey` (atau sesuai petunjuk hosting Anda).
   * **Value:** Masukkan string panjang kode kunci DKIM yang Anda salin dari hosting.
3. **DMARC Record (TXT):**
   * **Type:** `TXT`
   * **Name / Host:** `_dmarc`
   * **Value:** `v=DMARC1; p=none; rua=mailto:admin@domainanda.com`

