# Implementation Plan - Sistem Pengelolaan Data Nilai Mahasiswa (CLI Edition PHP V3.1)

Implementasi aplikasi CLI PHP murni untuk pengelolaan data nilai mahasiswa berdasarkan spesifikasi Product Requirement Document (PRD) V3.1.

## User Review Required

> [!IMPORTANT]
> - **Aturan Algoritma Ketat**: Pengurutan, pencarian, dan penentuan nilai ekstrem tidak menggunakan fungsi native PHP (`sort`, `rsort`, `array_search`, `max`, `min`). Semua algoritma (Bubble Sort, Selection Sort, Linear Search, Straight Max-Min) diimplementasikan manual menggunakan perulangan/iterasi.
> - **Format File Storage**: File penyimpanan menggunakan format Markdown/teks `mahasiswa.md` dengan delimiter pipe (`NIM|Nama|Nilai`).
> - **Modularitas**: Seluruh fungsionalitas dibungkus ke dalam fungsi-fungsi modular sesuai spesifikasi PRD V3.1.

## Proposed Changes

### Core CLI Application

#### [NEW] [index.php](file:///home/razan/Documents/Algoritma%20Sp/Codes/UAS%20Algo%20SP/index.php)
File utama aplikasi PHP CLI yang mengintegrasikan seluruh skema fungsi dan menjalankan siklus hidup inisialisasi (*Startup Protocol*), navigasi menu interaktif, pengolahan data, algoritma pengurutan/pencarian, serta menu administrasi.

Modul dan fungsi yang akan diimplementasikan:
1. **Startup Lifecycle & Handshake**:
   - `checkFileExistence()`: Memeriksa keberadaan `mahasiswa.md`. Jika tidak ada, memberikan peringatan error dan meminta perintah pengguna (`buat`/`bikin`) untuk membuat file kosong baru.
   - `createEmptyFile()`: Membuat file `mahasiswa.md` baru.

2. **Data I/O Engine**:
   - `loadData()`: Membaca file `mahasiswa.md` dan memparsing setiap baris (`NIM|Nama|Nilai`) menjadi array terstruktur.
   - `saveData($data)`: Menuliskan kembali array data mahasiswa ke `mahasiswa.md` dengan format pipe delimiter.

3. **Navigation Engine**:
   - `mainMenu()`: Menampilkan menu utama dan mengelola loop input navigasi.
   - `parseMenuInput($input)`: Memetakan input kata kunci / alias (case-insensitive) ke ID Menu resmi (1-9, 0).

4. **Business Logic & Algorithms (Tanpa Native Functions)**:
   - `inputData()`: Menambah data mahasiswa baru dengan validasi tipe data dan pencegahan duplikasi NIM.
   - `displayData($data)`: Menampilkan data dalam format tabel CLI yang rapi.
   - `linearSearch($data, $keyword)`: Pencarian iteratif manual berdasarkan NIM atau Nama.
   - `maxMin($data)`: Algoritma Straight Max-Min manual untuk menentukan nilai tertinggi dan terendah beserta data mahasiswanya.
   - `statistics($data)`: Menghitung total mahasiswa, rata-rata nilai, statistik kelulusan (nilai $\ge 60$), serta summary performa.
   - `bubbleSort($data)`: Pengurutan manual Bubble Sort berdasarkan nilai (ascending/descending) dan menghitung jumlah iterasi/swap.
   - `selectionSort($data)`: Pengurutan manual Selection Sort berdasarkan nilai dan menghitung jumlah iterasi/swap.

5. **Admin & Maintenance Suite (Menu 9)**:
   - `adminMenu()`: Sub-menu interaktif untuk pengujian dan pemeliharaan.
   - `seedData()`: Penjanaan data sintetis 30 mahasiswa (NIM `25101001`-`25101030`, sintesis nama depan/belakang, nilai terdistribusi 35–100).
   - `resetData()`: Mengosongkan data dan memulihkan ke 30 data awal seeder.
   - `verifyFileIntegrity()`: Memeriksa struktur delimiter dan keabsahan nilai numerik pada `mahasiswa.md`.
   - `benchmarkAlgorithms($data)`: Menjalankan benchmark perbandingan kinerja Bubble Sort vs Selection Sort (jumlah iterasi dan waktu eksekusi dalam mikrosekon).
   - `generateBackup()`: Membuat file snapshot cadangan `mahasiswa_backup_YYYYMMDD_HHMMSS.md`.

## Verification Plan

### Automated / CLI Verification
1. **Pengecekan Lint & Sintaks PHP**:
   - Menjalankan `php -l index.php` untuk memastikan tidak ada kesalahan sintaks.
2. **Pengujian Startup Protocol**:
   - Menjalankan aplikasi saat `mahasiswa.md` belum ada, memastikan indikator error muncul dan perintah `buat`/`bikin` bekerja dengan benar.
3. **Pengujian Menu Admin & Seeder**:
   - Menjalankan seeder otomatis untuk generate 30 data mahasiswa dan memverifikasi isi `mahasiswa.md`.
4. **Pengujian Algoritma Manual**:
   - Verifikasi pengurutan (Bubble & Selection), pencarian linear, Straight Max-Min, dan kalkulasi statistik tanpa menggunakan fungsi array native PHP (`sort`, `max`, `min`, `array_search`).
5. **Pengujian Benchmark & Backup**:
   - Memastikan pengujian komparatif performa algoritma menghasilkan metrik durasi ($\mu s$) dan iterasi, serta file backup berhasil dibuat.
