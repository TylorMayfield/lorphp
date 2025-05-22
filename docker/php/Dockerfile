FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy configurations
COPY ../nginx.conf /etc/nginx/nginx.conf
COPY ../php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ../supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ../start.sh /start.sh

# Make start script executable
RUN chmod +x /start.sh

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory
COPY . /var/www/html

# Install application dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage


# Expose the default HTTP and HTTPS ports (actual port is set at runtime by Kinsta)
EXPOSE 80 443

# Use the start script as entrypoint
CMD ["/start.sh"]
