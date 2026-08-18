#!/usr/bin/env bash
# ==============================================================================
# Script Peluncur Aplikasi Sistem Pengelolaan Data Nilai Mahasiswa (CLI V3.1)
# ==============================================================================

# Dapatkan direktori script saat ini
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Periksa apakah PHP CLI terinstall
if ! command -v php >/dev/null 2>&1; then
    echo "======================================================="
    echo "[ERROR] PHP tidak ditemukan di sistem Linux Anda!"
    echo "Silakan install PHP CLI terlebih dahulu, contoh:"
    echo "  Ubuntu/Debian : sudo apt update && sudo apt install php-cli"
    echo "  Arch Linux    : sudo pacman -S php"
    echo "  Fedora        : sudo dnf install php-cli"
    echo "======================================================="
    exit 1
fi

# Jalankan aplikasi PHP CLI
php "$SCRIPT_DIR/index.php" "$@"
