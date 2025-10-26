#!/bin/sh

# Hentikan skrip jika ada perintah yang gagal
set -e

# Hapus cache lama & buat cache baru menggunakan Env Vars dari Render
echo "===== (ENTRYPOINT) MEMBERSIHKAN DAN MEMBUAT CACHE KONFIGURASI ====="
php artisan config:clear
php artisan config:cache

# Menjalankan migrasi database (sekarang akan menggunakan pgsql)
echo "===== (ENTRYPOINT) MENJALANKAN MIGRATION DATABASE ====="
php artisan migrate --force

# Menyalakan server Apache
echo "===== (ENTRYPOINT) MENYALAKAN SERVER APACHE ====="
apache2-foreground