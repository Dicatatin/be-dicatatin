# Menggunakan PHP 8.4 sesuai kebutuhan Laravel 12
FROM php:8.4-cli

# Install dependencies (termasuk libpq-dev untuk PostgreSQL & ca-certificates untuk SSL)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    ca-certificates \
    curl \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip

# sertifikat SSL agar PHP bisa melakukan request HTTPS ke Cloudinary/FastAPI
RUN update-ca-certificates

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
