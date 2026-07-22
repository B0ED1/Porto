# Product Requirements Document (PRD)
**Project Name:** Interactive Heart Landing Page ("To The Prettiest Girl I Know" - Edisi V2)
**Document Status:** Draft
**Target Platform:** Web Browser (Mobile & Desktop Responsive)

## 1. Overview & Objective
Proyek ini bertujuan untuk membangun sebuah *landing page* interaktif romantis yang menampilkan animasi partikel teks berbentuk hati. Halaman ini mensimulasikan antarmuka terminal/hacker yang kemudian bertransisi menjadi animasi penuh cinta yang diiringi oleh musik latar. 

## 2. Tech Stack
* **Markup:** HTML5
* **Styling:** Tailwind CSS (via CDN atau npm untuk efisiensi styling)
* **Logic & Animation:** Vanilla JavaScript, HTML5 Canvas API (untuk performa animasi partikel yang mulus), dan HTML5 Audio API.

## 3. Fitur Utama (Core Features)
Berdasarkan referensi video, berikut adalah fitur esensial yang harus ada:

1.  **Terminal Boot-up Sequence:** * Layar hitam dengan teks bergaya *monospace*.
    * Efek mengetik otomatis (Typing Effect) untuk teks: `[SYSTEM] Initializing heart.PROTOCOL...` dan `[status]`.
2.  **Decrypt Button (Interaction Trigger):**
    * Tombol di tengah layar bertuliskan "DECRYPT MESSAGE" beserta ikon gembok kecil.
    * **Fungsi Krusial:** Klik pada tombol ini akan memicu dua hal secara bersamaan: memulai animasi partikel dan *memutar lagu latar* (mengatasi pemblokiran autoplay dari browser).
3.  **Text-Particle Heart Animation:**
    * Setelah tombol diklik, ratusan elemen teks (misal: "I love you") bertebaran dari luar layar dan berkumpul di tengah membentuk siluet hati yang besar.
    * Warna teks menggunakan gradasi merah ke merah muda dengan efek *glow* (Neon effect).
4.  **Background Music Player:**
    * Pemutaran lagu secara otomatis setelah tombol *Decrypt* ditekan.

## 4. Fitur Tambahan (Enhancements - "Make it more attractive")
Untuk membuat proyek ini lebih spesial dan menarik dari sekadar tiruan video:

* **Pulsing Heartbeat Effect:** Setelah partikel selesai membentuk hati, keseluruhan bentuk hati tersebut berdenyut (*scale up and down*) mengikuti ketukan atau tempo lambat.
* **Dynamic Color Shifting:** Warna partikel "I love you" perlahan berubah-ubah di spektrum merah, *crimson*, dan *magenta* menggunakan CSS *hue-rotate* atau manipulasi warna di Canvas.
* **Hidden Personal Message (Easter Egg):** * Setelah hati terbentuk dan berdenyut selama 5 detik, teks di dalam hati memudar (fade out) dan digantikan oleh paragraf pujian atau pesan personal yang romantis khusus untuknya.
* **Floating Fireflies:** Latar belakang gelap tidak benar-benar kosong, melainkan memiliki partikel debu/kunang-kunang kecil berwarna emas yang melayang lambat di *background*.
* **Custom Audio Fade-in:** Audio tidak langsung memutar dengan volume 100%, melainkan *fade-in* perlahan dari 0% ke 100% dalam 3 detik pertama agar transisi terasa sinematik.

## 5. User Flow
1.  **Landing:** *User* membuka link. Layar gelap.
2.  **Init:** Animasi teks terminal mengetik satu per satu.
3.  **Call to Action:** Tombol "DECRYPT MESSAGE" muncul dengan efek *pulse* perlahan.
4.  **Interaction:** *User* menekan tombol.
5.  **Execution:** * Antarmuka terminal menghilang (*fade out*).
    * Musik latar mulai diputar (*fade in*).
    * Partikel teks berterbangan dan menyatu membentuk hati (*Canvas Animation*).
6.  **Climax:** Hati berdenyut, menampilkan efek *glow*.
7.  **Resolution:** Pesan personal muncul di tengah layar.

## 6. Panduan Implementasi Teknis Singkat
* **Tailwind CSS:** Gunakan *utility classes* seperti `bg-gray-900`, `text-red-500`, `animate-pulse`, `flex`, `justify-center`, `items-center`, `h-screen` untuk struktur dasar dan styling terminal.
* **Canvas vs DOM:** Untuk animasi ribuan teks "I love you" yang membentuk hati, **sangat disarankan menggunakan HTML `<canvas>`**. Menggunakan elemen DOM (`<span>` atau `<div>`) sebanyak itu akan membuat browser patah-patah (*lag*). 
* **Matematika Bentuk Hati:** Untuk menentukan titik kumpul partikel di Canvas, gunakan persamaan parametrik hati standar dalam JavaScript:
    * $x = 16 \sin^3(t)$
    * $y = 13 \cos(t) - 5 \cos(2t) - 2 \cos(3t) - \cos(4t)$
    * (Gunakan parameter $t$ dari $0$ hingga $2\pi$ untuk memetakan koordinat, lalu kalikan dengan faktor skala untuk menyesuaikan ukuran layar).
* **Audio Element:** Siapkan file lagu (misalnya `.mp3`) dan *load* menggunakan `<audio id="bg-music" src="lagu.mp3" loop></audio>`. Panggil `document.getElementById('bg-music').play()` tepat di dalam *event listener* tombol Decrypt.