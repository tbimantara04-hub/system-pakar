#!/bin/sh
set -e

echo "===== (ENTRYPOINT) MENGHAPUS FILE .env LOKAL (jika ada) ====="
rm -f .env

echo "===== (ENTRYPOINT) MEMBERSIHKAN CACHE KONFIGURASI ====="
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "===== (ENTRYPOINT) MEMBUAT STORAGE LINK ====="
php artisan storage:link

# --- PERBAIKAN DI SINI ---
# Gunakan migrate:fresh untuk mereset database yang error
echo "===== (ENTRYPOINT) MENJALANKAN MIGRATE:FRESH ====="
php artisan migrate:fresh --force
# --- AKHIR PERBAIKAN ---

echo "===== (ENTRYPOINT) MENYALAKAN SERVER APACHE ====="
apache2-foreground