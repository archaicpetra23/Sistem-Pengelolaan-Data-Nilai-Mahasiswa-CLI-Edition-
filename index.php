<?php
/**
 * Sistem Pengelolaan Data Nilai Mahasiswa (CLI Edition)
 * Dokumen Versi: 3.7 (V3.7 - Full Inline Documentation & Presentation Ready)
 * Platform / Environment: Pure PHP CLI (Tanpa Framework / Library Pihak Ketiga)
 * Author / Developer: Razan Rafi Akmaluzzuhair
 * AI Personal Assistant: Gemini (Google DeepMind)
 */

// Menentukan konstanta jalur file penyimpanan data utama berbasis Markdown (mahasiswa.md)
define('STORAGE_FILE', __DIR__ . '/mahasiswa.md');

// ==========================================
// 0. Screen & Terminal Helpers (Pengelola Tampilan CLI)
// ==========================================

/**
 * Membersihkan layar terminal menggunakan Kode Escape ANSI
 */
function clearScreen(): void
{
    // Mengirimkan kode escape ANSI \033[2J untuk hapus layar dan \033[1;1H untuk posisi kursor ke pojok kiri atas
    echo "\033[2J\033[1;1H";
}

/**
 * Menahan tampilan output tabel/analisis agar tidak langsung terhapus
 */
function pauseForReading(): void
{
    // Mencetak garis pemisah
    echo "\n-------------------------------------------------------\n";
    // Menampilkan instruksi jeda baca bagi pengguna
    echo "Tekan [Enter] untuk kembali...";
    // Membaca masukan tombol Enter dari standar input (keyboard)
    fgets(STDIN);
}

// ==========================================
// 1. Startup Lifecycle & File Handshake Protocol (Siklus Inisialisasi)
// ==========================================

/**
 * Memeriksa keberadaan file penyimpanan saat aplikasi pertama kali dijalankan
 */
function checkFileExistence(): void
{
    // Mengecek apakah file mahasiswa.md belum ada di direktori lokal
    if (!file_exists(STORAGE_FILE)) {
        // Membersihkan layar terminal
        clearScreen();
        // Menampilkan bingkai indikator kesalahan file hilang
        echo "=======================================================\n";
        echo "[ERROR] File 'mahasiswa.md' tidak ditemukan dalam direktori lokal!\n";
        echo "=======================================================\n";
        
        // Loop interaktif meminta konfirmasi pembuatan file dari pengguna
        while (true) {
            // Meminta pengguna mengetik perintah konfirmasi
            echo "Ketik 'Yes' atau 'Y' untuk membuat file mahasiswa.md kosong baru (atau 'batal' untuk keluar): ";
            // Membaca masukan teks keyboard dan membuang spasi di awal/akhir
            $input = trim(fgets(STDIN));
            // Mengubah seluruh huruf masukan menjadi huruf kecil untuk perbandingan tidak kaku
            $inputLower = strtolower($input);
            
            // Memeriksa jika masukan sesuai kata kunci pembuatan (yes, y, buat, atau bikin)
            if ($inputLower === 'yes' || $inputLower === 'y' || $inputLower === 'buat' || $inputLower === 'bikin') {
                // Memanggil fungsi pembuat file kosong
                createEmptyFile();
                // Keluar dari perulangan prompt
                break;
            // Memeriksa jika pengguna ingin membatalkan inisialisasi
            } elseif ($inputLower === 'batal' || $inputLower === 'cancel') {
                // Menampilkan pesan pembatalan
                echo "[INFO] Inisialisasi dibatalkan. Keluar dari program.\n";
                // Menghentikan eksekusi skrip PHP sepenuhnya
                exit(0);
            } else {
                // Menampilkan peringatan jika perintah tidak dikenali
                echo "[PERINGATAN] Masukan tidak dikenali. Ketik 'Yes' atau 'Y' untuk melanjutkan.\n";
            }
        }
    }
}

/**
 * Membuat file fisik mahasiswa.md dalam kondisi kosong (0 bytes)
 */
function createEmptyFile(): void
{
    // Menuliskan string kosong ke file STORAGE_FILE (mahasiswa.md)
    file_put_contents(STORAGE_FILE, "");
}

// ==========================================
// 2. Data I/O Engine (Mesin Pembaca & Penyimpan Data File)
// ==========================================

/**
 * Membaca data dari mahasiswa.md dan memparsingnya menjadi Array Terstruktur dalam memori
 *
 * @return array Array berisi koleksi entitas mahasiswa ['nim' => string, 'nama' => string, 'nilai' => float]
 */
function loadData(): array
{
    // Jika file penyimpanan belum ada, kembalikan array kosong
    if (!file_exists(STORAGE_FILE)) {
        return [];
    }

    // Membaca seluruh konten teks dari file mahasiswa.md
    $content = file_get_contents(STORAGE_FILE);
    // Jika isi file gagal dibaca atau kosong, kembalikan array kosong
    if ($content === false || trim($content) === '') {
        return [];
    }

    // Memecah seluruh teks file menjadi array per baris menggunakan pemisah baris baru (\n)
    $lines = explode("\n", $content);
    // Inisialisasi array penampung data mahasiswa terstruktur
    $data = [];

    // Mengiterasi setiap baris string hasil pemecahan
    foreach ($lines as $line) {
        // Membuang spasi/karakter newline tambahan di awal dan akhir baris
        $line = trim($line);
        // Mengabaikan baris jika kosong atau tidak mengandung karakter delimiter pipe '|'
        if ($line === '' || strpos($line, '|') === false) {
            continue;
        }

        // Memecah kolom record berdasarkan pembatas pipe '|'
        $parts = explode('|', $line);
        // Memastikan record memiliki minimal 3 bagian kolom (NIM, Nama, Nilai)
        if (count($parts) >= 3) {
            // Mengambil dan membersihkan NIM (Kolom 1)
            $nim = trim($parts[0]);
            // Mengambil dan membersihkan Nama Mahasiswa (Kolom 2)
            $nama = trim($parts[1]);
            // Mengambil dan membersihkan Nilai (Kolom 3)
            $nilaiStr = trim($parts[2]);

            // Memvalidasi agar NIM & Nama tidak kosong serta Nilai berbentuk numerik valid
            if ($nim !== '' && $nama !== '' && is_numeric($nilaiStr)) {
                // Menambahkan elemen array asosiatif ke dalam variabel $data
                $data[] = [
                    'nim' => $nim,          // Simpan NIM
                    'nama' => $nama,        // Simpan Nama
                    'nilai' => (float)$nilaiStr // Konversi string nilai menjadi float
                ];
            }
        }
    }

    // Mengembalikan array terstruktur yang siap diolah oleh algoritma
    return $data;
}

/**
 * Menuliskan kembali seluruh data array dari memori ke dalam file mahasiswa.md
 *
 * @param array $data Array koleksi mahasiswa
 */
function saveData(array $data): void
{
    // Inisialisasi array penampung baris teks
    $lines = [];
    // Mengiterasi setiap entitas mahasiswa
    foreach ($data as $item) {
        // Menggabungkan NIM, Nama, dan Nilai menjadi satu baris berformat pembatas pipe (NIM|Nama|Nilai)
        $lines[] = $item['nim'] . '|' . $item['nama'] . '|' . $item['nilai'];
    }
    // Menggabungkan seluruh baris dengan newline (\n) lalu menuliskan secara permanen ke file mahasiswa.md
    file_put_contents(STORAGE_FILE, implode("\n", $lines) . ($lines ? "\n" : ""));
}

// ==========================================
// 3. User Interface & Navigation Engine (Engine Navigasi & Pemetaan Menu)
// ==========================================

/**
 * Memetakan masukan string atau angka pengguna menjadi ID Menu resmi (1-9, 0)
 *
 * @param string $input Masukan mentah dari keyboard
 * @return int|null ID Menu resmi atau null jika tidak ada alias yang cocok
 */
function parseMenuInput(string $input): ?int
{
    // Membersihkan masukan dan mengubah menjadi huruf kecil
    $cleanInput = strtolower(trim($input));

    // Array pemetaan alias kata kunci masukan ke ID Menu resmi
    $mapping = [
        '1' => ['1', 'input', 'input data', 'input data mahasiswa'],
        '2' => ['2', 'tampilkan', 'tampilkan data', 'display', 'lihat'],
        '3' => ['3', 'cari', 'cari mahasiswa', 'linear search', 'search'],
        '4' => ['4', 'max-min', 'nilai tertinggi', 'max min', 'tertinggi terendah', 'nilai tertinggi & terendah'],
        '5' => ['5', 'statistik', 'statistik nilai', 'stats', 'analisis'],
        '6' => ['6', 'bubble', 'bubble sort', 'urut bubble'],
        '7' => ['7', 'selection', 'selection sort', 'urut selection'],
        '8' => ['8', 'hapus', 'hapus data', 'hapus data mahasiswa', 'delete', 'remove'],
        '9' => ['9', 'admin', 'test', 'admin menu', 'test menu'],
        '0' => ['0', 'keluar', 'exit', 'quit']
    ];

    // Mencari kecocokan masukan dengan daftar alias
    foreach ($mapping as $menuId => $aliases) {
        foreach ($aliases as $alias) {
            // Jika masukan cocok dengan salah satu alias
            if ($cleanInput === $alias) {
                // Kembalikan ID menu bertipe integer
                return (int)$menuId;
            }
        }
    }

    // Kembalikan null jika masukan tidak dikenali
    return null;
}

/**
 * Menampilkan Menu Utama dan mengontrol alur aplikasi secara berulang (Loop Utama)
 */
function mainMenu(): void
{
    // Variabel penampung pesan notifikasi status (Seamless Notice Banner)
    $noticeMsg = '';

    // Perulangan utama program (Main Execution Loop)
    while (true) {
        // Membersihkan layar terminal sebelum mencetak menu
        clearScreen();
        // Mencetak header visual Menu Utama
        echo "=======================================================\n";
        echo "   SISTEM PENGELOLAAN DATA NILAI MAHASISWA (CLI V3.7)  \n";
        echo "=======================================================\n";
        echo " [1] Input Data Mahasiswa\n";
        echo " [2] Tampilkan Data Mahasiswa\n";
        echo " [3] Cari Mahasiswa (Linear Search)\n";
        echo " [4] Nilai Tertinggi & Terendah (Straight Max-Min)\n";
        echo " [5] Statistik Nilai Mahasiswa\n";
        echo " [6] Urutkan Nilai (Bubble Sort)\n";
        echo " [7] Urutkan Nilai (Selection Sort)\n";
        echo " [8] Hapus Data Mahasiswa\n";
        echo " [9] Admin / Test Menu\n";
        echo " [0] Keluar Program\n";
        echo "-------------------------------------------------------\n";

        // Tampilkan pesan notifikasi langsung di bawah menu jika ada
        if ($noticeMsg !== '') {
            echo "$noticeMsg\n";
            echo "-------------------------------------------------------\n";
            // Reset notifikasi setelah ditampilkan agar tidak muncul kembali di opsi berikutnya
            $noticeMsg = '';
        }

        // Meminta masukan pilihan menu dari pengguna
        echo "Pilih menu (nomor atau kata kunci): ";

        // Membaca masukan dari stdin
        $input = fgets(STDIN);
        // Jika pembacaan stdin gagal (EOF), keluar dari loop
        if ($input === false) {
            break;
        }

        // Memetakan masukan teks menjadi ID Menu numerik
        $menuId = parseMenuInput($input);

        // Jika masukan tidak valid, isi notifikasi error dan ulang loop
        if ($menuId === null) {
            $noticeMsg = "[ERROR] Pilihan menu tidak valid! Silakan coba lagi.";
            continue;
        }

        // Percabangan eksekusi fitur berdasarkan ID Menu
        switch ($menuId) {
            case 1:
                clearScreen(); // Bersihkan layar
                $noticeMsg = inputData(); // Jalankan input data dan ambil pesan notifikasi status
                break;
            case 2:
                clearScreen(); // Bersihkan layar
                $data = loadData(); // Load data dari file ke array memori
                displayData($data); // Tampilkan tabel data
                pauseForReading(); // Tahan layar untuk dibaca pengguna
                break;
            case 3:
                clearScreen(); // Bersihkan layar
                // Loop perulangan jika kata kunci pencarian kosong
                while (true) {
                    echo "Masukkan NIM atau Nama Mahasiswa yang dicari (atau 'batal' untuk cancel): ";
                    $keyword = trim(fgets(STDIN));

                    // Pengecekan pembatalan pencarian
                    if (isCancelInput($keyword)) {
                        $noticeMsg = "[INFO] Pencarian dibatalkan.";
                        break;
                    }

                    // Pengecekan kata kunci kosong
                    if ($keyword === '') {
                        echo "[ERROR] Kata kunci pencarian tidak boleh kosong! Silakan coba lagi.\n";
                        continue;
                    }

                    $data = loadData(); // Load data dari file
                    linearSearch($data, $keyword); // Eksekusi pencarian linear search
                    pauseForReading(); // Tahan layar untuk membaca hasil
                    break;
                }
                break;
            case 4:
                clearScreen(); // Bersihkan layar
                $data = loadData(); // Load data dari file
                maxMin($data); // Eksekusi algoritma Straight Max-Min
                pauseForReading(); // Tahan layar untuk dibaca
                break;
            case 5:
                clearScreen(); // Bersihkan layar
                $data = loadData(); // Load data dari file
                statistics($data); // Hitung & tampilkan statistik nilai
                pauseForReading(); // Tahan layar untuk dibaca
                break;
            case 6:
                clearScreen(); // Bersihkan layar
                $data = loadData(); // Load data dari file
                handleSortMenu($data, 'bubble'); // Pengurutan manual Bubble Sort
                pauseForReading(); // Tahan layar untuk membaca hasil & metrik
                break;
            case 7:
                clearScreen(); // Bersihkan layar
                $data = loadData(); // Load data dari file
                handleSortMenu($data, 'selection'); // Pengurutan manual Selection Sort
                pauseForReading(); // Tahan layar untuk membaca hasil & metrik
                break;
            case 8:
                clearScreen(); // Bersihkan layar
                $noticeMsg = deleteData(); // Jalankan hapus data dan ambil pesan status
                break;
            case 9:
                adminMenu(); // Buka sub-menu Admin & Maintenance
                break;
            case 0:
                clearScreen(); // Bersihkan layar
                echo "Terima kasih telah menggunakan sistem ini. Sampai jumpa!\n\n";
                exit(0); // Keluar dari program
        }
    }
}

// ==========================================
// 4. Helper & Input Handling (Fungsi Pembantu Validasi)
// ==========================================

/**
 * Memeriksa apakah kata kunci yang diinputkan pengguna adalah perintah pembatalan
 *
 * @param string $input String masukan
 * @return bool True jika masukan adalah batal/cancel, False jika bukan
 */
function isCancelInput(string $input): bool
{
    $clean = strtolower(trim($input));
    // Memeriksa kata kunci batal atau cancel
    return ($clean === 'batal' || $clean === 'cancel');
}

/**
 * Meminta dialog konfirmasi ketika pemicu kata pembatalan terdeteksi
 *
 * @param string $fieldName Nama kolom data (NIM/Nama/Nilai)
 * @return bool True jika pengguna mengonfirmasi pembatalan
 */
function confirmCancel(string $fieldName): bool
{
    echo "Apakah Anda benar-benar ingin membatalkan pengisian $fieldName? (y/n): ";
    $ans = strtolower(trim(fgets(STDIN)));
    // Kembalikan true jika pengguna menjawab 'y', 'ya', 'batal', atau 'cancel'
    return ($ans === 'y' || $ans === 'ya' || $ans === 'batal' || $ans === 'cancel');
}

// ==========================================
// 5. Core Features & Custom Manual Algorithms (Fitur Utama & Algoritma Manual)
// ==========================================

/**
 * Meminta masukan entri mahasiswa baru (NIM, Nama, Nilai) dengan validasi tipe data lengkap
 *
 * @return string Pesan status hasil operasi input
 */
function inputData(): string
{
    echo "=======================================================\n";
    echo "             INPUT DATA MAHASISWA BARU                 \n";
    echo "=======================================================\n";
    echo "(Ketik 'batal' atau 'cancel' pada masukan mana saja untuk membatalkan)\n\n";

    // 1. Validasi Input NIM
    $nim = '';
    while (true) {
        echo "Masukkan NIM (contoh: 25101001): ";
        $input = trim(fgets(STDIN));
        
        // Pengecekan pembatalan input NIM
        if (isCancelInput($input)) {
            if (confirmCancel("NIM")) {
                return "[INFO] Proses input data mahasiswa dibatalkan.";
            } else {
                continue;
            }
        }

        // Pengecekan NIM kosong
        if ($input === '') {
            echo "[ERROR] NIM tidak boleh kosong!\n";
            continue;
        }

        // Mencegah injeksi karakter delimiter '|' yang dapat merusak struktur file
        if (strpos($input, '|') !== false) {
            echo "[ERROR] NIM tidak boleh mengandung karakter '|'!\n";
            continue;
        }

        // Algoritma manual pengecekan duplikasi NIM (Case-Insensitive)
        $existingData = loadData();
        $isDuplicate = false;
        $inputLower = strtolower($input);
        for ($i = 0; $i < count($existingData); $i++) {
            if (strtolower($existingData[$i]['nim']) === $inputLower) {
                $isDuplicate = true;
                break;
            }
        }

        // Jika NIM terdeteksi duplikat, tampilkan error dan minta masukan ulang
        if ($isDuplicate) {
            echo "[ERROR] NIM '$input' sudah terdaftar dalam sistem! Gunakan NIM lain.\n";
            continue;
        }

        $nim = $input; // NIM valid tersimpan
        break;
    }

    // 2. Validasi Input Nama Mahasiswa
    $nama = '';
    while (true) {
        echo "Masukkan Nama Mahasiswa: ";
        $input = trim(fgets(STDIN));

        // Pengecekan pembatalan input Nama
        if (isCancelInput($input)) {
            if (confirmCancel("Nama")) {
                return "[INFO] Proses input data mahasiswa dibatalkan.";
            } else {
                continue;
            }
        }

        // Pengecekan Nama kosong
        if ($input === '') {
            echo "[ERROR] Nama mahasiswa tidak boleh kosong!\n";
            continue;
        }

        // Mencegah injeksi karakter delimiter '|'
        if (strpos($input, '|') !== false) {
            echo "[ERROR] Nama mahasiswa tidak boleh mengandung karakter '|'!\n";
            continue;
        }

        $nama = $input; // Nama valid tersimpan
        break;
    }

    // 3. Validasi Input Nilai Mahasiswa
    $nilai = 0.0;
    while (true) {
        echo "Masukkan Nilai (0 - 100): ";
        $input = trim(fgets(STDIN));

        // Pengecekan pembatalan input Nilai
        if (isCancelInput($input)) {
            if (confirmCancel("Nilai")) {
                return "[INFO] Proses input data mahasiswa dibatalkan.";
            } else {
                continue;
            }
        }

        // Memastikan format desimal/integer standar tanpa notasi eksponen ilmiah (seperti 1e2)
        if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $input)) {
            echo "[ERROR] Nilai harus berupa angka desimal/integer standar (contoh: 85 atau 85.5)!\n";
            continue;
        }

        $val = (float)$input;
        // Validasi rentang nilai harus berada di antara 0 sampai 100
        if ($val < 0 || $val > 100) {
            echo "[ERROR] Nilai harus berada dalam rentang 0 hingga 100!\n";
            continue;
        }

        $nilai = $val; // Nilai valid tersimpan
        break;
    }

    // Memuat data aktif, menambahkan entitas mahasiswa baru, dan menyimpan ke file
    $existingData = loadData();
    $existingData[] = [
        'nim' => $nim,
        'nama' => $nama,
        'nilai' => $nilai
    ];

    saveData($existingData); // Tulis permanen ke mahasiswa.md
    return "[BERHASIL] Data mahasiswa '$nama' ($nim) berhasil disimpan!";
}

/**
 * Menghapus data mahasiswa berdasarkan NIM atau Nama secara manual tanpa fungsi array native (unset/array_splice)
 *
 * @return string Pesan status notifikasi
 */
function deleteData(): string
{
    // Memuat data dari file
    $data = loadData();
    // Jika data kosong, kembalikan notifikasi
    if (count($data) === 0) {
        return "[INFO] Data mahasiswa kosong. Tidak ada data yang dapat dihapus.";
    }

    echo "=======================================================\n";
    echo "               HAPUS DATA MAHASISWA                    \n";
    echo "=======================================================\n";
    echo "(Ketik 'batal' atau 'cancel' untuk membatalkan)\n\n";

    // Loop meminta kata kunci NIM/Nama mahasiswa yang akan dihapus
    $keyword = '';
    while (true) {
        echo "Masukkan NIM atau Nama Mahasiswa yang ingin dihapus: ";
        $keyword = trim(fgets(STDIN));

        // Pengecekan pembatalan
        if (isCancelInput($keyword)) {
            return "[INFO] Proses penghapusan data dibatalkan.";
        }

        if ($keyword === '') {
            echo "[ERROR] Masukan NIM atau Nama tidak boleh kosong! Silakan coba lagi.\n";
            continue;
        }

        break;
    }

    $keywordLower = strtolower($keyword);
    $foundIndexes = [];

    // Algoritma manual pencarian data mahasiswa yang cocok
    for ($i = 0; $i < count($data); $i++) {
        $nimLower = strtolower($data[$i]['nim']);
        $namaLower = strtolower($data[$i]['nama']);

        // Pengecekan kecocokan NIM atau Substring Nama
        if ($nimLower === $keywordLower || strpos($namaLower, $keywordLower) !== false) {
            $foundIndexes[] = $i; // Simpan indeks data yang cocok
        }
    }

    // Jika tidak ada data yang cocok
    if (count($foundIndexes) === 0) {
        return "[INFO] Tidak ditemukan data mahasiswa dengan NIM/Nama '$keyword'.";
    }

    // Menentukan mahasiswa mana yang dihapus jika terdapat multiple match
    $targetIndex = -1;
    if (count($foundIndexes) === 1) {
        $targetIndex = $foundIndexes[0];
    } else {
        // Tampilkan daftar pilihan jika ditemukan lebih dari 1 mahasiswa cocok
        echo "\nDitemukan beberapa data mahasiswa yang cocok:\n";
        for ($k = 0; $k < count($foundIndexes); $k++) {
            $idx = $foundIndexes[$k];
            echo sprintf(" [%d] NIM: %s | Nama: %s | Nilai: %.1f\n", ($k + 1), $data[$idx]['nim'], $data[$idx]['nama'], $data[$idx]['nilai']);
        }

        // Loop re-prompt saat pengguna menginput nomor tidak valid
        while (true) {
            echo "Pilih nomor mahasiswa yang akan dihapus (1 - " . count($foundIndexes) . ") atau ketik 'batal': ";
            $choiceInput = trim(fgets(STDIN));

            if (isCancelInput($choiceInput)) {
                return "[INFO] Proses penghapusan data dibatalkan.";
            }

            if (!is_numeric($choiceInput) || (int)$choiceInput < 1 || (int)$choiceInput > count($foundIndexes)) {
                echo "[ERROR] Pilihan nomor tidak valid! Silakan masukkan angka antara 1 hingga " . count($foundIndexes) . ".\n";
                continue;
            }

            $targetIndex = $foundIndexes[(int)$choiceInput - 1];
            break;
        }
    }

    // Tampilkan rincian entitas yang akan dihapus dan minta konfirmasi
    $targetStudent = $data[$targetIndex];
    echo "\nDetail Mahasiswa yang akan dihapus:\n";
    echo " NIM   : " . $targetStudent['nim'] . "\n";
    echo " Nama  : " . $targetStudent['nama'] . "\n";
    echo " Nilai : " . $targetStudent['nilai'] . "\n";
    echo "\nApakah Anda yakin ingin menghapus data tersebut? (y/n / 'batal'): ";
    
    $confirm = trim(fgets(STDIN));
    $confirmLower = strtolower($confirm);

    // Batal jika konfirmasi bukan 'y' atau 'ya'
    if ($confirmLower !== 'y' && $confirmLower !== 'ya') {
        return "[INFO] Penghapusan data dibatalkan.";
    }

    // Rekonstruksi ulang array tanpa entitas yang dihapus secara manual (Tanpa fungsi unset/array_splice native)
    $newData = [];
    for ($i = 0; $i < count($data); $i++) {
        if ($i !== $targetIndex) {
            $newData[] = $data[$i]; // Masukkan elemen yang tidak dihapus
        }
    }

    saveData($newData); // Simpan struktur baru ke mahasiswa.md
    return "[BERHASIL] Data mahasiswa '" . $targetStudent['nama'] . "' (" . $targetStudent['nim'] . ") berhasil dihapus!";
}

/**
 * Mencetak tabel representasi data mahasiswa secara rapi pada CLI
 *
 * @param array $data Koleksi data mahasiswa
 */
function displayData(array $data): void
{
    // Jika array kosong
    if (count($data) === 0) {
        echo "[INFO] Data mahasiswa kosong. Silakan input data terlebih dahulu atau jalankan Seeder pada Menu Admin.\n";
        return;
    }

    // Cetak Header Tabel ASCII
    echo "=======================================================\n";
    echo "              DAFTAR DATA MAHASISWA                    \n";
    echo "=======================================================\n";
    echo "+------+------------+--------------------------------+--------+--------------+\n";
    echo sprintf("| %-4s | %-10s | %-30s | %-6s | %-12s |\n", "No", "NIM", "Nama Mahasiswa", "Nilai", "Keterangan");
    echo "+------+------------+--------------------------------+--------+--------------+\n";

    // Loop iterasi setiap data mahasiswa untuk dicetak per baris tabel
    for ($i = 0; $i < count($data); $i++) {
        $item = $data[$i];
        // Penentuan status kelulusan (Keterangan: Lulus jika Nilai >= 60, else Tidak Lulus)
        $ket = ($item['nilai'] >= 60) ? "Lulus" : "Tidak Lulus";
        // Pemotongan nama jika melebihi 30 karakter agar tabel tetap rapi
        $namaCut = (strlen($item['nama']) > 30) ? substr($item['nama'], 0, 27) . "..." : $item['nama'];
        
        // Cetak baris data dengan format kolom rata kiri/kanan yang teratur
        echo sprintf(
            "| %-4d | %-10s | %-30s | %-6.1f | %-12s |\n",
            ($i + 1),
            $item['nim'],
            $namaCut,
            $item['nilai'],
            $ket
        );
    }

    // Cetak Footer Tabel ASCII
    echo "+------+------------+--------------------------------+--------+--------------+\n";
    echo "Total Data: " . count($data) . " entitas mahasiswa.\n";
}

/**
 * Melakukan algoritma Linear Search manual berurutan tanpa fungsi native array_search()
 *
 * @param array $data Koleksi data mahasiswa
 * @param string $keyword Kata kunci pencarian (NIM / Nama)
 */
function linearSearch(array $data, string $keyword): void
{
    $keywordLower = strtolower($keyword);
    $results = []; // Array penampung hasil pencarian

    // Iterasi perulangan manual Linear Search dari elemen pertama hingga terakhir
    for ($i = 0; $i < count($data); $i++) {
        $item = $data[$i];
        $nimLower = strtolower($item['nim']);
        $namaLower = strtolower($item['nama']);

        // Pengecekan substring pencarian pada NIM atau Nama menggunakan strpos
        if (strpos($nimLower, $keywordLower) !== false || strpos($namaLower, $keywordLower) !== false) {
            $results[] = $item; // Masukkan ke hasil pencarian
        }
    }

    echo "=======================================================\n";
    echo "       HASIL PENCARIAN MAHASISWA ('$keyword')          \n";
    echo "=======================================================\n";
    if (count($results) === 0) {
        echo "[INFO] Tidak ditemukan data mahasiswa yang cocok dengan kata kunci '$keyword'.\n";
    } else {
        // Tampilkan tabel dari hasil pencarian yang ditemukan
        displayData($results);
    }
}

/**
 * Menentukan nilai maksimum dan minimum melalui Algoritma Straight Max-Min manual (Tanpa max() & min())
 *
 * @param array $data Koleksi data mahasiswa
 */
function maxMin(array $data): void
{
    $total = count($data);
    if ($total === 0) {
        echo "[INFO] Data mahasiswa kosong. Tidak dapat menghitung nilai ekstrem.\n";
        return;
    }

    // Inisialisasi awal nilai Max & Min dari elemen pertama array ($data[0])
    $maxVal = $data[0]['nilai'];
    $minVal = $data[0]['nilai'];

    // Perulangan manual Straight Max-Min membandingkan setiap elemen dari indeks ke-1 sampai N
    for ($i = 1; $i < $total; $i++) {
        $currentNilai = $data[$i]['nilai'];
        // Jika nilai saat ini lebih besar dari maxVal, perbarui maxVal
        if ($currentNilai > $maxVal) {
            $maxVal = $currentNilai;
        }
        // Jika nilai saat ini lebih kecil dari minVal, perbarui minVal
        if ($currentNilai < $minVal) {
            $minVal = $currentNilai;
        }
    }

    // Mengumpulkan seluruh mahasiswa yang memiliki nilai persis sama dengan Nilai Max & Min
    $maxStudents = [];
    $minStudents = [];

    for ($i = 0; $i < $total; $i++) {
        if ($data[$i]['nilai'] == $maxVal) {
            $maxStudents[] = $data[$i];
        }
        if ($data[$i]['nilai'] == $minVal) {
            $minStudents[] = $data[$i];
        }
    }

    // Menampilkan hasil analisis Straight Max-Min
    echo "=======================================================\n";
    echo "       HASIL ANALISIS NILAI EXTREM (STRAIGHT MAX-MIN)  \n";
    echo "=======================================================\n";
    
    echo "NILAI TERTINGGI (MAX): " . sprintf("%.1f", $maxVal) . "\n";
    echo "Daftar Mahasiswa Nilai Tertinggi:\n";
    for ($i = 0; $i < count($maxStudents); $i++) {
        echo " - NIM: " . $maxStudents[$i]['nim'] . " | Nama: " . $maxStudents[$i]['nama'] . "\n";
    }

    echo "\nNILAI TERENDAH (MIN): " . sprintf("%.1f", $minVal) . "\n";
    echo "Daftar Mahasiswa Nilai Terendah:\n";
    for ($i = 0; $i < count($minStudents); $i++) {
        echo " - NIM: " . $minStudents[$i]['nim'] . " | Nama: " . $minStudents[$i]['nama'] . "\n";
    }
    echo "=======================================================\n";
}

/**
 * Menghitung rerata kelas, total kelulusan, dan statistik secara manual tanpa fungsi math/array native (array_sum)
 *
 * @param array $data Koleksi data mahasiswa
 */
function statistics(array $data): void
{
    $total = count($data);
    if ($total === 0) {
        echo "[INFO] Data mahasiswa kosong. Tidak ada statistik yang dapat ditampilkan.\n";
        return;
    }

    // Inisialisasi variabel statistik
    $sum = 0.0;
    $lulusCount = 0;
    $tidakLulusCount = 0;
    $maxVal = $data[0]['nilai'];
    $minVal = $data[0]['nilai'];

    // Perulangan akumulasi nilai dan penghitungan kondisi kelulusan
    for ($i = 0; $i < $total; $i++) {
        $nilai = $data[$i]['nilai'];
        $sum += $nilai; // Akumulasi total nilai manual

        // Hitung kelulusan (Batas Lulus >= 60)
        if ($nilai >= 60) {
            $lulusCount++;
        } else {
            $tidakLulusCount++;
        }

        // Pengecekan ekstrem
        if ($nilai > $maxVal) {
            $maxVal = $nilai;
        }
        if ($nilai < $minVal) {
            $minVal = $nilai;
        }
    }

    // Kalkulasi rata-rata (Mean = Total Sum / Total Mahasiswa)
    $mean = $sum / $total;
    // Kalkulasi persentase kelulusan
    $passPercentage = ($lulusCount / $total) * 100;

    // Tampilkan ringkasan statistik kelas
    echo "=======================================================\n";
    echo "              STATISTIK NILAI MAHASISWA                \n";
    echo "=======================================================\n";
    echo sprintf(" Total Mahasiswa       : %d orang\n", $total);
    echo sprintf(" Total Akumulasi Nilai : %.2f\n", $sum);
    echo sprintf(" Rata-rata Nilai Kelas : %.2f\n", $mean);
    echo sprintf(" Nilai Tertinggi (Max) : %.1f\n", $maxVal);
    echo sprintf(" Nilai Terendah (Min)  : %.1f\n", $minVal);
    echo sprintf(" Jumlah Lulus (>= 60)  : %d orang (%.1f%%)\n", $lulusCount, $passPercentage);
    echo sprintf(" Jumlah Tidak Lulus    : %d orang (%.1f%%)\n", $tidakLulusCount, 100 - $passPercentage);
    echo "=======================================================\n";
}

/**
 * Mengurutkan array berdasarkan nilai menggunakan Algoritma Bubble Sort manual (Tanpa sort(), rsort(), usort())
 *
 * @param array $data Data mahasiswa
 * @param string $order Urutan ('asc' / 'desc')
 * @return array Hasil pengurutan beserta metrik perulangan, penukaran, dan durasi eksekusi
 */
function bubbleSort(array $data, string $order = 'desc'): array
{
    $startTime = microtime(true); // Catat waktu mulai dalam mikrosekon
    $n = count($data);
    $iterations = 0; // Penampung hitungan iterasi
    $swaps = 0;      // Penampung hitungan penukaran (swaps)
    $arr = $data;

    // Perulangan luar Bubble Sort dari pass 0 sampai N-1
    for ($i = 0; $i < $n - 1; $i++) {
        $swapped = false; // Flag penanda apakah ada penukaran pada pass ini
        // Perulangan dalam membandingkan elemen bertetangga
        for ($j = 0; $j < $n - $i - 1; $j++) {
            $iterations++; // Tambah hitungan iterasi
            
            // Kondisi perbandingan berdasarkan opsi Ascending / Descending
            $condition = ($order === 'asc') 
                ? ($arr[$j]['nilai'] > $arr[$j + 1]['nilai'])
                : ($arr[$j]['nilai'] < $arr[$j + 1]['nilai']);

            // Jika kondisi terpenuhi, lakukan swap (penukaran elemen) secara manual
            if ($condition) {
                $temp = $arr[$j];            // Simpan sementara elemen saat ini
                $arr[$j] = $arr[$j + 1];     // Ganti elemen saat ini dengan elemen sebelahnya
                $arr[$j + 1] = $temp;        // Pindahkan elemen sementara ke sebelahnya
                $swaps++;                    // Tambah hitungan swap
                $swapped = true;             // Set flag swapped menjadi true
            }
        }
        // Jika tidak ada elemen yang ditukar dalam satu pass, array sudah terurut (Optimasi Bubble Sort)
        if (!$swapped) {
            break;
        }
    }

    // Hitung durasi waktu eksekusi dalam mikrosekon (µs)
    $duration = (microtime(true) - $startTime) * 1000000;

    return [
        'sortedData' => $arr,
        'iterations' => $iterations,
        'swaps' => $swaps,
        'duration' => $duration
    ];
}

/**
 * Mengurutkan array berdasarkan nilai menggunakan Algoritma Selection Sort manual (Tanpa sort(), rsort(), usort())
 *
 * @param array $data Data mahasiswa
 * @param string $order Urutan ('asc' / 'desc')
 * @return array Hasil pengurutan beserta metrik perulangan, penukaran, dan durasi eksekusi
 */
function selectionSort(array $data, string $order = 'desc'): array
{
    $startTime = microtime(true); // Catat waktu mulai dalam mikrosekon
    $n = count($data);
    $iterations = 0; // Hitungan iterasi
    $swaps = 0;      // Hitungan swaps
    $arr = $data;

    // Perulangan luar Selection Sort menentukan posisi indeks target yang akan diisi
    for ($i = 0; $i < $n - 1; $i++) {
        $targetIdx = $i; // Asumsikan indeks target awal adalah $i
        // Perulangan dalam mencari elemen minimum/maksimum di sisa array
        for ($j = $i + 1; $j < $n; $j++) {
            $iterations++; // Tambah hitungan iterasi
            
            // Kondisi perbandingan Ascending / Descending
            $condition = ($order === 'asc')
                ? ($arr[$j]['nilai'] < $arr[$targetIdx]['nilai'])
                : ($arr[$j]['nilai'] > $arr[$targetIdx]['nilai']);

            // Perbarui targetIdx jika ditemukan nilai yang lebih sesuai
            if ($condition) {
                $targetIdx = $j;
            }
        }

        // Jika targetIdx berubah, lakukan penukaran (swap) manual dengan elemen pada posisi $i
        if ($targetIdx !== $i) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$targetIdx];
            $arr[$targetIdx] = $temp;
            $swaps++; // Tambah hitungan swap
        }
    }

    // Hitung durasi waktu eksekusi dalam mikrosekon (µs)
    $duration = (microtime(true) - $startTime) * 1000000;

    return [
        'sortedData' => $arr,
        'iterations' => $iterations,
        'swaps' => $swaps,
        'duration' => $duration
    ];
}

/**
 * Menangani alur dan tampilan menu pengurutan (Bubble Sort / Selection Sort)
 *
 * @param array $data Data mahasiswa
 * @param string $algoType Tipe algoritma ('bubble' / 'selection')
 */
function handleSortMenu(array $data, string $algoType): void
{
    if (count($data) === 0) {
        echo "[INFO] Data mahasiswa kosong. Tidak ada data untuk diurutkan.\n";
        return;
    }

    $algoName = ($algoType === 'bubble') ? "Bubble Sort" : "Selection Sort";
    echo "=======================================================\n";
    echo "       URUTKAN NILAI MAHASISWA ($algoName)             \n";
    echo "=======================================================\n";
    echo "Pilihan Urutan:\n";
    echo " [1] Descending (Nilai Tertinggi ke Terendah)\n";
    echo " [2] Ascending (Nilai Terendah ke Tertinggi)\n";
    echo "Pilih urutan [1/2] (Default: 1, atau 'batal' untuk cancel): ";

    $choice = trim(fgets(STDIN));
    if (isCancelInput($choice)) {
        echo "[INFO] Pengurutan data dibatalkan.\n";
        return;
    }

    $order = ($choice === '2') ? 'asc' : 'desc';

    // Panggil algoritma pengurutan manual sesuai pilihan
    if ($algoType === 'bubble') {
        $result = bubbleSort($data, $order);
    } else {
        $result = selectionSort($data, $order);
    }

    $orderLabel = ($order === 'desc') ? "Descending" : "Ascending";
    echo "\n>>> Hasil Pengurutan $algoName ($orderLabel) <<<\n";
    displayData($result['sortedData']); // Tampilkan tabel hasil urutan

    // Tampilkan metrik performa algoritma (Iterasi, Swap, dan Durasi Eksekusi)
    echo "\nMetrik Performa Algoritma:\n";
    echo " - Total Iterasi : " . $result['iterations'] . " perulangan\n";
    echo " - Total Swaps   : " . $result['swaps'] . " kali penukaran\n";
    echo sprintf(" - Durasi Eksekusi: %.2f µs (mikrosekon)\n", $result['duration']);
}

// ==========================================
// 6. Admin & Maintenance Suite (Sub-menu Admin Opsi 9)
// ==========================================

/**
 * Penjana data sintetis 30 mahasiswa terstruktur (NIM 25101001-25101030), nama variatif, dan nilai terdistribusi
 *
 * @return string Pesan status notifikasi
 */
function seedData(): string
{
    // Bank Nama Depan untuk penjelas variasi nama
    $firstNameBank = [
        'Adriansyah', 'Budi', 'Clarissa', 'Dewi', 'Farhan', 'Grace', 'Hendra', 'Indah',
        'Joko', 'Kevin', 'Lestari', 'Muhammad', 'Nabila', 'Oscar', 'Putri', 'Rizky',
        'Siti', 'Taufik', 'Utami', 'Anton', 'Wahyu', 'Yulia', 'Zulkarnain'
    ];

    // Bank Nama Belakang / Keluarga
    $lastNameBank = [
        'Pratama', 'Wijaya', 'Santoso', 'Kusuma', 'Saputra', 'Hidayat', 'Lestari',
        'Nugroho', 'Wibowo', 'Kurniawan', 'Ramadhan', 'Putri', 'Siregar', 'Nasution',
        'Simanjuntak', 'Morger'
    ];

    // Preset Nilai Sintetis terdistribusi (rentang 35 hingga 100)
    $scorePreset = [
        98.5, 95.0, 92.0, 88.5, 85.0, 82.5, 80.0, 78.0, 75.5, 74.0,
        72.0, 70.0, 68.5, 66.0, 65.0, 63.5, 61.0, 60.0, 58.5, 55.0,
        52.0, 48.0, 45.0, 42.5, 40.0, 38.0, 35.0, 89.0, 91.5, 77.0
    ];

    $seedRecords = [];
    $totalRecords = 30; // Standar volume 30 mahasiswa

    // Perulangan penjanaan 30 entitas sintetis
    for ($i = 0; $i < $totalRecords; $i++) {
        $seq = sprintf("%03d", $i + 1); // Format urutan 001-030
        $nim = "25101" . $seq;          // NIM Terstruktur: 25 (Tahun) 101 (Prodi) 001-030 (Urut)

        $fn = $firstNameBank[$i % count($firstNameBank)];
        $ln = $lastNameBank[($i * 3) % count($lastNameBank)];
        $nama = $fn . " " . $ln; // Sintesis Nama Depan + Nama Belakang

        $nilai = $scorePreset[$i % count($scorePreset)];

        $seedRecords[] = [
            'nim' => $nim,
            'nama' => $nama,
            'nilai' => $nilai
        ];
    }

    saveData($seedRecords); // Simpan ke file mahasiswa.md
    return "[BERHASIL] Seeding data selesai! 30 data mahasiswa terstruktur berhasil dimasukkan ke mahasiswa.md.";
}

/**
 * Menjalankan pengujian komparatif performa antara Bubble Sort vs Selection Sort
 *
 * @param array $data Data mahasiswa
 */
function benchmarkAlgorithms(array $data): void
{
    if (count($data) === 0) {
        echo "[INFO] Data mahasiswa kosong. Tidak ada data untuk dibenchmark.\n";
        return;
    }

    echo "--- Running Benchmark & Algorithm Performance Test ---\n";
    echo "Menguji " . count($data) . " data mahasiswa...\n\n";

    // Jalankan dan ukur Bubble Sort
    $bubbleResult = bubbleSort($data, 'desc');
    // Jalankan dan ukur Selection Sort
    $selectionResult = selectionSort($data, 'desc');

    // Cetak Tabel Hasil Komparasi Performa
    echo "+-------------------+-----------------+---------------+----------------+\n";
    echo sprintf("| %-17s | %-15s | %-13s | %-14s |\n", "Algoritma", "Waktu Eksekusi", "Total Iterasi", "Total Swaps");
    echo "+-------------------+-----------------+---------------+----------------+\n";
    echo sprintf(
        "| %-17s | %-12.2f µs | %-13d | %-14d |\n",
        "Bubble Sort",
        $bubbleResult['duration'],
        $bubbleResult['iterations'],
        $bubbleResult['swaps']
    );
    echo sprintf(
        "| %-17s | %-12.2f µs | %-13d | %-14d |\n",
        "Selection Sort",
        $selectionResult['duration'],
        $selectionResult['iterations'],
        $selectionResult['swaps']
    );
    echo "+-------------------+-----------------+---------------+----------------+\n";

    // Tampilkan kesimpulan algoritma mana yang lebih efisien dalam durasi waktu
    if ($bubbleResult['duration'] < $selectionResult['duration']) {
        echo "[KESIMPULAN] Bubble Sort tampil lebih cepat pada pengujian ini.\n";
    } elseif ($selectionResult['duration'] < $bubbleResult['duration']) {
        echo "[KESIMPULAN] Selection Sort tampil lebih cepat pada pengujian ini.\n";
    } else {
        echo "[KESIMPULAN] Kedua algoritma memberikan durasi eksekusi seimbang.\n";
    }
}

/**
 * Memeriksa tiap baris record untuk mendeteksi integritas format delimiter atau cacat data
 */
function verifyFileIntegrity(): void
{
    if (!file_exists(STORAGE_FILE)) {
        echo "[ERROR] File 'mahasiswa.md' tidak ditemukan!\n";
        return;
    }

    $content = file_get_contents(STORAGE_FILE);
    $lines = explode("\n", $content);

    echo "--- Verifikasi Integritas File & Validasi Format ---\n";
    $totalLines = 0;
    $validRecords = 0;
    $errors = [];

    // Mengiterasi dan memeriksa integritas tiap baris
    foreach ($lines as $index => $line) {
        $lineNumber = $index + 1;
        $trimmed = trim($line);

        if ($trimmed === '') {
            continue; // Abaikan baris kosong
        }

        $totalLines++;
        $parts = explode('|', $trimmed);

        // Pengecekan kecukupan kolom delimiter pipe
        if (count($parts) < 3) {
            $errors[] = "Baris $lineNumber: Format delimiter '|' kurang dari 3 kolom.";
            continue;
        }

        $nim = trim($parts[0]);
        $nama = trim($parts[1]);
        $nilai = trim($parts[2]);

        if ($nim === '') {
            $errors[] = "Baris $lineNumber: NIM kosong.";
        }
        if ($nama === '') {
            $errors[] = "Baris $lineNumber: Nama kosong.";
        }
        if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $nilai)) {
            $errors[] = "Baris $lineNumber: Nilai '$nilai' bukan angka numerik desimal/integer valid.";
        }

        if ($nim !== '' && $nama !== '' && preg_match('/^[0-9]+(\.[0-9]+)?$/', $nilai)) {
            $validRecords++;
        }
    }

    // Tampilkan ringkasan status integritas file
    echo "Total Baris Terbaca : $totalLines\n";
    echo "Record Valid        : $validRecords\n";
    echo "Total Error/Defect  : " . count($errors) . "\n";

    if (count($errors) > 0) {
        echo "\nRincian Error Integritas:\n";
        foreach ($errors as $err) {
            echo " [!] $err\n";
        }
    } else {
        echo "\n[STATUS] Integritas file SANGAT BAIK! Seluruh record memenuhi format standard (NIM|Nama|Nilai).\n";
    }
}

/**
 * Membuat salinan cadangan snapshot file dengan penamaan timestamp dinamis
 *
 * @return string Pesan status notifikasi
 */
function generateBackup(): string
{
    if (!file_exists(STORAGE_FILE)) {
        return "[ERROR] File 'mahasiswa.md' tidak ditemukan! Tidak ada file untuk dibackup.";
    }

    $timestamp = date('Ymd_His'); // Tanggal & Jam saat ini
    $backupFilename = __DIR__ . "/mahasiswa_backup_{$timestamp}.md";

    // Menyalin file penyimpanan ke nama file cadangan baru
    if (copy(STORAGE_FILE, $backupFilename)) {
        return "[BERHASIL] Snapshot backup berhasil dibuat: " . basename($backupFilename);
    } else {
        return "[ERROR] Gagal membuat file backup!";
    }
}

/**
 * Sub-menu pengontrol alur eksekusi fitur-fitur pengujian dan pemeliharaan (Menu Admin Opsi 9)
 */
function adminMenu(): void
{
    $adminNoticeMsg = ''; // Variabel penampung notifikasi sub-menu admin

    while (true) {
        clearScreen(); // Bersihkan layar
        echo "=======================================================\n";
        echo "         ADMIN & MAINTENANCE SUITE (MENU 9)            \n";
        echo "=======================================================\n";
        echo " [1] Auto-Create & Auto-Seed Data (30 Sample Data)\n";
        echo " [2] Reset Data / Clean State (Restore 30 Sample Data)\n";
        echo " [3] Verifikasi Integritas File & Validasi Format\n";
        echo " [4] Benchmark & Test Algorithm Performance\n";
        echo " [5] Generate Backup Data (Snapshot File)\n";
        echo " [0] Kembali ke Menu Utama\n";
        echo "-------------------------------------------------------\n";

        // Tampilkan pesan notifikasi langsung di bawah menu admin jika ada
        if ($adminNoticeMsg !== '') {
            echo "$adminNoticeMsg\n";
            echo "-------------------------------------------------------\n";
            $adminNoticeMsg = ''; // Reset notifikasi setelah ditampilkan
        }

        echo "Pilih opsi admin [0-5]: ";

        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                // Seeding langsung mengembalikan pesan notifikasi ke menu admin tanpa menahan Enter
                $adminNoticeMsg = seedData();
                break;
            case '2':
                createEmptyFile();
                seedData();
                $adminNoticeMsg = "[BERHASIL] Data di-reset ke kondisi awal (30 sampel data seeder).";
                break;
            case '3':
                clearScreen();
                verifyFileIntegrity();
                pauseForReading();
                break;
            case '4':
                clearScreen();
                $data = loadData();
                benchmarkAlgorithms($data);
                pauseForReading();
                break;
            case '5':
                $adminNoticeMsg = generateBackup();
                break;
            case '0':
                return; // Kembali ke Menu Utama
            default:
                $adminNoticeMsg = "[ERROR] Pilihan menu admin tidak valid!";
                break;
        }
    }
}

// ==========================================
// Main Entry Point (Titik Awal Eksekusi Program)
// ==========================================

// 1. Jalankan Pengecekan Siklus Inisialisasi Keberadaan File
checkFileExistence();
// 2. Jalankan Menu Utama Aplikasi CLI
mainMenu();

// ==============================================================================
// Developed by Razan Rafi Akmaluzzuhair
// AI Personal Assistant: Gemini (Google DeepMind)
// Copyright (c) 2026 Razan Rafi Akmaluzzuhair. All Rights Reserved.
// Sistem Pengelolaan Data Nilai Mahasiswa (CLI Edition PHP V3.7)
// Developed for Sumatif IF30412 Algoritma & Pemecahan Masalah
// ==============================================================================
