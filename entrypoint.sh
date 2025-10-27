#!/bin/sh

# Hentikan skrip jika ada perintah yang gagal
set -e

# --- LANGKAH DEBUGGING ---
# Cetak semua environment variable yang diterima skrip ini
echo "===== (ENTRYPOINT) DEBUGGING ENV VARS ====="
env | grep DB_ # Tampilkan hanya variabel DB_ untuk mempermudah
echo "===== (ENTRYPOINT) SELESAI DEBUGGING ENV VARS ====="
# --- AKHIR LANGKAH DEBUGGING ---

# HAPUS file .env yang ter-copy dari lokal agar tidak menimpa envVars Render
echo "===== (ENTRYPOINT) MENGHAPUS FILE .env LOKAL (jika ada) ====="
rm -f .env

# Hapus cache konfigurasi agar Laravel membaca envVars dari Render
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