# Sistem Pengelolaan Data Nilai Mahasiswa (CLI Edition)

Perangkat lunak berbasis Command Line Interface (CLI) menggunakan PHP murni tanpa dependensi framework, library pihak ketiga, maupun sistem manajemen basis data (RDBMS). Aplikasi ini dirancang untuk pengolahan, pencarian, pengurutan, dan analisis data nilai mahasiswa secara efisien menggunakan media penyimpanan file teks tunggal berformat Markdown (`mahasiswa.md`).

Proyek ini dikembangkan sebagai pemenuhan Tugas Sumatif Mata Kuliah **IF30412 Algoritma & Pemecahan Masalah**.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Batasan Arsitektur](#batasan-arsitektur)
- [Spesifikasi Algoritma Manual](#spesifikasi-algoritma-manual)
- [Struktur Berkas](#struktur-berkas)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Panduan Penggunaan](#panduan-penggunaan)
- [Pengujian Performa & Benchmark](#pengujian-performa--benchmark)
- [Penulis & Hak Cipta](#penulis--hak-cipta)

---

## Fitur Utama

1. **Input Data Mahasiswa Baru**
   - Mendukung pencatatan NIM, Nama, dan Nilai.
   - Dilengkapi validasi tipe data, batas rentang nilai (0 - 100), sanitasi karakter delimiter, serta pengecekan duplikasi NIM secara case-insensitive.
   - Menyediakan opsi pembatalan (`batal` / `cancel`) pada setiap tahapan masukan.

2. **Tampilkan Data Mahasiswa**
   - Menampilkan seluruh data mahasiswa dalam format tabel ASCII yang terstruktur dan rapi.
   - Mengalkulasi dan menampilkan keterangan kelulusan secara eksplisit (Lulus jika Nilai >= 60, Tidak Lulus jika Nilai < 60).

3. **Cari Mahasiswa (Linear Search)**
   - Pencarian data berbasis NIM atau Nama Mahasiswa (termasuk pencarian substring case-insensitive).

4. **Nilai Tertinggi & Terendah (Straight Max-Min)**
   - Menentukan nilai maksimum dan minimum kelas beserta daftar mahasiswa pemilik nilai tersebut.

5. **Statistik Nilai Mahasiswa**
   - Menyajikan ringkasan kelas mencakup total mahasiswa, akumulasi nilai, rata-rata kelas, nilai tertinggi/terendah, jumlah mahasiswa lulus/tidak lulus, serta persentase kelulusan.

6. **Urutkan Nilai (Bubble Sort & Selection Sort)**
   - Pengurutan data berdasarkan nilai secara Ascending maupun Descending.
   - Menyajikan metrik performa algoritma mencakup total iterasi perulangan, jumlah penukaran (*swaps*), dan durasi eksekusi dalam mikrosekon ($\mu s$).

7. **Hapus Data Mahasiswa**
   - Penghapusan record mahasiswa berbasis NIM atau Nama dengan konfirmasi dialog dan dukungan multiple match selection.

8. **Admin & Maintenance Suite (Menu 9)**
   - **Auto-Seed Data**: Penjanaan sintetis 30 data mahasiswa terstruktur (NIM `25101001` hingga `25101030`) dengan variasi nama dan distribusi nilai.
   - **Reset Data / Clean State**: Pemulihan kondisi penyimpanan ke 30 sampel data seeder bawaan.
   - **Verifikasi Integritas File**: Pemeriksaan integritas format baris file `mahasiswa.md` dan validitas angka.
   - **Benchmark Performa Algoritma**: Pengujian pengujian komparatif berdampingan antara Bubble Sort vs Selection Sort.
   - **Generate Backup Data**: Pembuatan snapshot salinan cadangan file dengan penamaan timestamp dinamis (`mahasiswa_backup_YYYYMMDD_HHMMSS.md`).

---

## Batasan Arsitektur

Aplikasi ini dibangun dengan mematuhi batasan teknis dan aturan akademis berikut:

- **Bahasa Pemrograman**: Pure PHP CLI tanpa framework, library pihak ketiga, atau database SQL/NoSQL.
- **Media Penyimpanan Utama**: File teks tunggal berformat Markdown `mahasiswa.md` dengan delimiter pipe (`NIM|Nama|Nilai`).
- **Data Memori**: Seluruh manipulasi data dijalankan pada struktur data array dinamis di dalam memori setelah dimuat dari file.
- **Pembatasan Fungsi Native Array**: Dilarang keras menggunakan fungsi array bawaan PHP untuk pencarian, pengurutan, dan penentuan ekstrem, seperti:
  - *Sorting*: `sort()`, `rsort()`, `asort()`, `arsort()`, `usort()`, `ksort()`.
  - *Search & Math*: `array_search()`, `max()`, `min()`, `array_sum()`, `unset()`, `array_splice()`.

---

## Spesifikasi Algoritma Manual

Seluruh logika pemrosesan data diimplementasikan secara eksplisit menggunakan algoritma manual:

### 1. Straight Max-Min
Metode penentuan nilai ekstrem dilakukan melalui iterasi tunggal membandingkan elemen saat ini terhadap nilai maksimum (`maxVal`) dan minimum (`minVal`) yang diinisialisasi dari elemen pertama array ($A[0]$).

### 2. Linear Search
Pencarian sekuensial mengiterasi seluruh elemen array dari indeks $0$ hingga $N-1$, membandingkan substring NIM dan Nama menggunakan perbandingan string manual.

### 3. Bubble Sort
Pengurutan dilakukan dengan membandingkan pasangan elemen bertetangga ($A[j]$ dan $A[j+1]$) dan melakukan penukaran variabel sementara (*swapping*) jika urutan tidak sesuai. Dilengkapi optimasi penghentian dini (*early exit*) jika dalam satu pass tidak terjadi penukaran.

### 4. Selection Sort
Pengurutan dilakukan dengan mencari indeks elemen ekstrem di sisa subarray belum terurut ($j = i+1 \dots N-1$), kemudian menukarkan elemen ekstrem tersebut ke posisi indeks target $i$.

---

## Struktur Berkas

```text
UAS Algo SP/
├── index.php                 # Kode sumber utama aplikasi PHP CLI
├── run.sh                    # Script peluncur otomatis untuk Linux
├── mahasiswa.md              # File penyimpanan data utama (Format: NIM|Nama|Nilai)
├── docs/
│   └── bug.md                # Dokumen inventarisir dan log perbaikan bug (V3.3)
└── .agents/
    └── AGENTS.md             # Aturan dan konfigurasi persona kolaborasi
```

---

## Persyaratan Sistem

- Operating System: Linux / macOS / Windows (dengan lingkungan Bash/CLI).
- PHP Engine: PHP CLI versi 7.4 atau versi 8.x yang lebih baru.

---

## Panduan Penggunaan

### 1. Menjalankan Aplikasi via Shell Script (Linux / macOS)

Buka terminal pada direktori proyek dan jalankan perintah berikut:

```bash
chmod +x run.sh
./run.sh
```

### 2. Menjalankan Aplikasi via PHP CLI Langsung

```bash
php index.php
```

---

## Pengujian Performa & Benchmark

Pengujian dilakukan terhadap 30 data mahasiswa terstruktur menggunakan fitur Admin Benchmark (Menu `9` -> Opsi `4`). Berikut adalah contoh hasil pengujian komparatif:

| Algoritma | Durasi Eksekusi | Total Iterasi | Total Penukaran (*Swaps*) |
| --- | --- | --- | --- |
| **Bubble Sort** | ~12.87 $\mu s$ | 429 perulangan | 68 kali |
| **Selection Sort** | ~10.01 $\mu s$ | 435 perulangan | 26 kali |

---

## Penulis & Hak Cipta

- **Pengembang Utama / Author**: Razan Rafi Akmaluzzuhair
- **AI Personal Assistant**: Gemini (Google DeepMind)

Hak Cipta (c) 2026 Razan Rafi Akmaluzzuhair. Seluruh hak cipta dilindungi undang-undang.
