#!/bin/sh

# Hentikan skrip jika ada perintah yang gagal
set -e

# --- LANGKAH DEBUGGING ---
# Cetak SEMUA environment variable yang diterima skrip ini
echo "===== (ENTRYPOINT) DEBUGGING SEMUA ENV VARS ====="
env
echo "===== (ENTRYPOINT) SELESAI DEBUGGING SEMUA ENV VARS ====="
# --- AKHIR LANGKAH DEBUGGING ---

# HAPUS file .env yang ter-copy dari lokal
echo "===== (ENTRYPOINT) MENGHAPUS FILE .env LOKAL (jika ada) ====="
rm -f .env

# Hapus cache konfigurasi
echo "===== (ENTRYPOINT) MEMBERSIHKAN CACHE KONFIGURASI ====="
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Menjalankan migrasi database
echo "===== (ENTRYPOINT) MENJALANKAN MIGRATION DATABASE ====="
php artisan migrate --force

# Menyalakan server Apache
echo "===== (ENTRYPOINT) MENYALAKAN SERVER APACHE ====="
apache2-foreground