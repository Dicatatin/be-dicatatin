FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    ca-certificates \
    curl \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip \
    && update-ca-certificates \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files dulu — layer caching supaya tidak install ulang tiap build
COPY composer.json composer.lock ./

# Install dependencies PHP — INI yang hilang sebelumnya
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy semua file project
COPY . .

# Generate autoload setelah semua file ada
RUN composer dump-autoload --optimize

EXPOSE ${PORT:-8000}

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}