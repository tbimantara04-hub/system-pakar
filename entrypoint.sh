#!/bin/sh

# Menjalankan migrasi database
echo "===== (ENTRYPOINT) MENJALANKAN MIGRATION DATABASE ====="
php artisan migrate --force

# Menyalakan server Apache
echo "===== (ENTRYPOINT) MENYALAKAN SERVER APACHE ====="
apache2-foreground