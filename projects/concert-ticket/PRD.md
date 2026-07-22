# Product Requirements Document (PRD)
**Proyek:** Web Manajemen Festival Musik (MusicFest CRUD)
[cite_start]**Konteks:** Ujian Akhir Semester Praktikum Pemrograman Web [cite: 3, 4]

## 1. Tujuan Proyek
[cite_start]Membangun sebuah aplikasi web responsif untuk mengelola data festival musik menggunakan implementasi fungsi CRUD (Create, Read, Update, Delete) secara penuh[cite: 10, 11]. [cite_start]Proyek ini merupakan tugas individu [cite: 6] [cite_start]yang harus dikerjakan secara orisinal[cite: 8].

## 2. Tumpukan Teknologi (Tech Stack)
* [cite_start]**Backend:** Native PHP [cite: 10, 16]
* [cite_start]**Database:** MySQL [cite: 18]
* [cite_start]**Frontend:** HTML, CSS dengan framework Bootstrap atau Tailwind CSS [cite: 13, 17]
* [cite_start]**Library Tambahan:** PHP Library standar [cite: 19]

## 3. Fitur Utama
Aplikasi ini diwajibkan memiliki fitur minimum berikut pada dashboard:
* [cite_start]**Create:** Formulir untuk menambahkan data pengunjung atau pemesanan tiket baru ke dalam database[cite: 10, 14].
* [cite_start]**Read:** Tabel yang menampilkan daftar pengunjung atau tiket yang telah terdaftar, mengambil data langsung dari MySQL[cite: 10, 14].
* [cite_start]**Update:** Halaman atau modal form untuk mengubah detail data yang sudah ada (misalnya mengganti kategori tiket)[cite: 10, 14].
* [cite_start]**Delete:** Tombol aksi untuk menghapus data dari sistem secara permanen[cite: 10, 14].
* [cite_start]**Responsive UI:** Antarmuka yang menyesuaikan dengan ukuran layar perangkat (desktop maupun mobile)[cite: 11, 13].

## 4. Struktur Database (Usulan)
**Tabel: `pendaftaran_tiket`**
* `id_tiket` (INT, Primary Key, Auto Increment)
* `nama_pemesan` (VARCHAR)
* `email` (VARCHAR)
* `kategori_tiket` (ENUM: 'Reguler', 'VIP', 'VVIP')
* `tanggal_pesan` (DATETIME)

## 5. Kriteria Pengumpulan (Deliverables)
* [cite_start]**Dokumen:** Laporan dalam bentuk PDF berisi deskripsi proyek, screenshot fitur, screenshot database, URL GitHub, dan URL video presentasi[cite: 21, 22].
* [cite_start]**Kode Sumber:** Diunggah ke GitHub, mencakup seluruh kode web dan file *database dump* (`.sql`)[cite: 32].
* **Video Presentasi:**
  * [cite_start]Diunggah ke YouTube dengan visibilitas *Unlisted*[cite: 31].
  * [cite_start]Durasi maksimal 10 menit[cite: 24].
  * Menampilkan wajah secara langsung[cite: 30].
  * [cite_start]Membahas deskripsi aplikasi, alur kerja (CRUD), dan daftar fitur[cite: 27, 28, 29].
  * [cite_start]Deskripsi YouTube harus memuat URL GitHub[cite: 32].
  * URL video wajib diserahkan di e-learning[cite: 33].