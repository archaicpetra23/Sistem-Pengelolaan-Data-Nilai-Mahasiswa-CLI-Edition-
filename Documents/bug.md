# Bug Audit & Resolution Log (`docs/bug.md`)

Dokumen ini mencatat daftar bug, edge case, dan status perbaikannya pada **Sistem Pengelolaan Data Nilai Mahasiswa (CLI Edition PHP V3.3)**.

---

## 📋 Daftar Bug & Status Perbaikan

| ID Bug | Kategori | Deskripsi Bug | Status | Solusi & Hasil Verifikasi |
| --- | --- | --- | --- | --- |
| **BUG-01** | Kritis | **Injeksi Karakter Delimiter Pipe (`\|`) pada Nama & NIM**<br>Memasukkan karakter `\|` pada nama/NIM merusak format file `mahasiswa.md`. | ✅ **Fixed** | Menambahkan penolakan input NIM/Nama yang mengandung `\|` dengan pesan error: `[ERROR] NIM/Nama tidak boleh mengandung karakter '\|'!`. |
| **BUG-02** | Sedang | **Tumpang Tindih Kata Kunci Pembatalan pada Nama Mahasiswa**<br>Nama 1 kata `"batal"`/`"cancel"` langsung memicu cancel. | ✅ **Fixed** | Menambahkan dialog konfirmasi `confirmCancel()` saat pemicu cancel terdeteksi sehingga input nama asli tidak sengaja ter-cancel. |
| **BUG-03** | Sedang | **Pengecekan Duplikasi NIM Case-Sensitive**<br>NIM `25101001A` dan `25101001a` dianggap berbeda. | ✅ **Fixed** | Pengecekan duplikasi NIM diubah menjadi *case-insensitive* (`strtolower`). |
| **BUG-04** | Minor | **UX Input Kosong pada Menu Pencarian**<br>Menekan Enter di menu Cari Mahasiswa langsung terlempar ke Menu Utama. | ✅ **Fixed** | Menambahkan *loop re-prompt* pada pencarian jika kata kunci kosong. |
| **BUG-05** | Minor | **Pilihan Nomor Hapus Tidak Valid Langsung Membatalkan**<br>Memilih nomor di luar jangkauan pada Hapus Data menggagalkan proses. | ✅ **Fixed** | Menambahkan *loop re-prompt* saat nomor pilihan tidak valid. |
| **BUG-06** | Minor | **Format Notasi Ilmiah (`1e2`) Lolos Validasi Nilai**<br>Fungsi `is_numeric()` menerima notasi eksponen/ilmiah. | ✅ **Fixed** | Menggunakan regex `preg_match('/^[0-9]+(\.[0-9]+)?$/', $input)` untuk menolak notasi eksponen dan hanya menerima angka desimal/int standar. |

---

## Ringkasan Verifikasi
Seluruh 6 bug di atas telah diuji menggunakan skenario masukan ekstrem dan dipastikan **100% Resolved & Stable** pada `index.php` (Versi 3.3).
