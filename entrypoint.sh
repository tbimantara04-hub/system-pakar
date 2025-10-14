#!/bin/bash

# --- 1. SETUP PRASYARAT ---

# Cek apakah file .env sudah ada. Jika belum, salin dari .env.example (atau buat yang kosong)
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Pastikan Application Key ter-generate. 
# Jika APP_KEY kosong atau belum ada, generate.
# Penting: Di Railway, APP_KEY biasanya sudah disuntikkan melalui environment variables.
# Namun, perintah ini memastikan ada key yang digunakan.
php artisan key:generate --force

# Bersihkan cache
php artisan cache:clear

# --- 2. MIGRASI DATABASE ---

echo "Starting database migration..."
# Jalankan migrasi. --force diperlukan di lingkungan produksi.
php artisan migrate --force

# --- 3. JALANKAN SERVER WEB ---

echo "Starting Laravel Development Server..."

# Menggunakan 'php artisan serve'
# Gunakan 0.0.0.0 agar dapat diakses dari luar container, dan gunakan variabel $PORT yang disediakan Railway.
exec php artisan serve --host=0.0.0.0 --port=$PORT
# 'exec' memastikan proses ini menggantikan shell, yang penting untuk Docker
