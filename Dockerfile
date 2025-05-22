FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    sqlite \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Create directory structure
RUN mkdir -p /var/www/html /run/nginx /run/php

# Copy application code
COPY . /var/www/html
WORKDIR /var/www/html

# Ensure storage directory exists and is writable
RUN mkdir -p /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 80

# Start supervisor (which manages nginx and php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
