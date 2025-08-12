FROM php:8.2-cli

# Install required PHP extensions and tools
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Composer files and install dependencies (without scripts/autoloader for caching)
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader

# Copy application files
COPY . .

# Install full dependencies
RUN composer install

# Generate Laravel APP_KEY (optional for dev environments)
RUN php artisan key:generate --force || true

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]