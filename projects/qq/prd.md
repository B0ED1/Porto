# 🎮 PRD: BeatPulse - Interactive Web Music Game

| Property | Detail |
| :--- | :--- |
| **Status** | Concept / Exploration |
| **Genre** | Audio-Visual Rhythm Game / Interactive Music Player |
| **Tech Stack** | HTML5 Canvas, Web Audio API, JavaScript (Vanilla / Phaser.js) |

---

## 1. Overview & Concept
**BeatPulse** adalah perpaduan antara pemutar musik dan game mini interaktif berbasis browser. Game ini membaca frekuensi audio secara real-time dari lagu yang diputar, lalu mengubah frekuensi tersebut menjadi ritme game (rintangan/item) yang harus direspons pemain.

---

## 2. Main Game Mechanics (Pilih Mode Game)

### Mode A: Beat Catcher (Casual / Chill)
* **Cara Main:** Bola/karakter utama berada di tengah layar. Setiap ada dentuman bass (*bass drop*), item bercahaya (*beat orb*) muncul dari tepi layar menuju tengah.
* **Tujuan:** Klik atau tekan tombol panah arah yang sesuai sebelum orb menyentuh lingkaran tengah untuk mendapat poin dan efek visual seru.

### Mode B: Neon Rhythm Runner (Arcade)
* **Cara Main:** 3 jalur (*lanes*) vertikal mirip Guitar Hero.
* **Tujuan:** Tekan tombol `A`, `S`, `D` atau `Left`, `Down`, `Right` saat ubin (*tile*) musik yang turun dari atas pas di garis pemicu. Speed ubin menyesuaikan tempo lagu (BPM).

---

## 3. Core Features

### 3.1. Dynamic Audio Engine 🔊
* **Real-time Beat Detection:** Menggunakan `AnalyserNode` dari Web Audio API untuk mendeteksi *pitch* dan *amplitude* tinggi (misal: saat bass memuncak, rintangan muncul lebih banyak).
* **Local Audio Upload:** Pemain bisa pakai lagu preset atau *upload* file MP3 favorit sendiri. Setiap lagu bakal menghasilkan level game yang unik secara otomatis!

### 3.2. Visuals & Feedback 🎆
* **Combo System:** Main tanpa meleset menambah perkalian skor (2x, 4x, 8x) dan memicu efek warna layar neon bergetar (*screen shake* halus).
* **Audio-Reactive Background:** Background Canvas membesar/mengecil dan berubah warna mengikuti beat musik secara real-time.

### 3.3. Game Controls
* **Keyboard:** `A` `S` `D` / Panah Arah untuk aksi game.
* **Spacebar:** Pause / Resume game & lagu.

---

## 4. Technical Architecture

* **Web Audio API (`AudioContext`):** Mengambil data array *frequency* lagu tiap frame.
* **HTML5 Canvas:** Tempat merender animasi game dan partikel dengan target smooth 60 FPS.
* **Game Loop:** Menggunakan `requestAnimationFrame` untuk menyelaraskan posisi rintangan dengan *clock time* lagu.