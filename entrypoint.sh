#!/bin/sh

# Hentikan skrip jika ada perintah yang gagal
set -e

# Hapus cache konfigurasi agar Laravel membaca envVars dari Render
echo "===== (ENTRYPOINT) MEMBERSIHKAN CACHE KONFIGURASI ====="
php artisan config:clear
# Kita juga bersihkan cache lain untuk jaga-jaga
php artisan route:clear
php artisan view:clear

# Menjalankan migrasi database (sekarang akan membaca envVars yg benar)
echo "===== (ENTRYPOINT) MENJALANKAN MIGRATION DATABASE ====="
php artisan migrate --force

# Menyalakan server Apache
echo "===== (ENTRYPOINT) MENYALAKAN SERVER APACHE ====="
apache2-foreground