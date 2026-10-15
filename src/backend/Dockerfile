# 1. Gunakan imej rasmi PHP 8.2 CLI berasaskan Linux
FROM php:8.4-cli

# 2. Pasang library sistem yang diperlukan oleh Laravel & SQLite
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libpq-dev

# 3. Pasang extension PHP untuk database dan string
RUN docker-php-ext-install pdo pdo_sqlite pdo_pgsql mbstring

# 4. Dapatkan Composer versi terkini dari imej rasmi Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Tetapkan folder kerja di dalam container
WORKDIR /var/www/backend

# 6. Salin semua kod projek backend ke dalam container
COPY . .

# 7. Pasang semua pakej Laravel melalui Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 8. Buka port 8000 untuk diakses dari luar
EXPOSE 8000

# 9. Arahan bila container mula hidup: Jalankan migration dan hidupkan server Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000