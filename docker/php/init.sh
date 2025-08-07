#!/bin/bash
set -e

if [ ! -f /var/www/html/composer.json ]; then
  echo "Laravel belum ada, instalasi dimulai..."
  cd /var/www/html
  composer create-project laravel/laravel="10.*" .
else
  echo "Laravel sudah terpasang, skip instalasi."
fi

cd /var/www/html

# Pasang Laravel Breeze jika belum pernah dipasang
if [ ! -d "vendor/laravel/breeze" ]; then
  echo "Memasang Laravel Breeze..."
  composer require laravel/breeze --dev
  php artisan breeze:install blade

  # Jalankan npm jika NodeJS tersedia
  if command -v npm &> /dev/null; then
    echo "Menjalankan npm install dan build..."
    npm install
    npm run build
  else
    echo "npm tidak ditemukan, lewati proses build frontend."
  fi
else
  echo "Breeze sudah terpasang, skip."
fi

# Ganti nilai DB_HOST, DB_DATABASE, DLL dari ENV Docker
echo "Menyesuaikan .env dengan variabel environment. Dengan koneksi ${DB_CONNECTION}"
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/" .env
sed -i "s/^#* *DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i 's/^#* *DB_PORT=3306*/DB_PORT=3306/' .env
sed -i "s/^#* *DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/^#* *DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/^#* *DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

cat .env | grep DB_

# atur kepemilikan dan hak akses direktori
chown -R $USER_ID:$GROUP_ID /var/www/html
echo "Mengatur hak akses direktori storage dan bootstrap/cache..."
mkdir -p /var/www/html/storage/logs
touch /var/www/html/storage/logs/laravel.log
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Membaca environment dari file .env..."
APP_ENV=$(grep ^APP_ENV= .env | cut -d '=' -f2 | tr -d '\r')

if [[ -z "$APP_ENV" ]]; then
    echo "APP_ENV tidak ditemukan di file .env. Menggunakan 'local' sebagai default."
    APP_ENV=local
fi

echo "Environment Laravel: $APP_ENV"

echo "Menjalankan optimisasi Laravel..."
composer validate --strict
composer install --optimize-autoloader --no-dev

php artisan migrate

if [ "$APP_ENV" = "production" ]; then
    echo "Mode production: menjalankan caching..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    echo "Mode development: membersihkan cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
fi

# Jalankan Apache agar container tetap aktif
exec apache2-foreground
