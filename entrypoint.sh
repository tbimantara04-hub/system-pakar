#!/bin/sh
set -e

echo "===== (ENTRYPOINT) MEMAKSA DEPLOY BARU - V2 =====" # <-- BARIS BARU

# HAPUS file .env yang ter-copy dari lokal
echo "===== (ENTRYPOINT) MENGHAPUS FILE .env LOKAL (jika ada) ====="
rm -f .env

# Hapus cache konfigurasi
echo "===== (ENTRYPOINT) MEMBERSIHKAN CACHE KONFIGURASI ====="
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Buat symbolic link untuk storage
echo "===== (ENTRYPOINT) MEMBUAT STORAGE LINK ====="
php artisan storage:link

# Menjalankan migrasi database
echo "===== (ENTRYPOINT) MENJALANKAN MIGRATE:FRESH ====="
php artisan migrate:fresh --force

# Menyalakan server Apache
echo "===== (ENTRYPOINT) MENYALAKAN SERVER APACHE ====="
apache2-foreground