FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    sqlite \
    sqlite-dev \
    supervisor \
    acl

# Install PHP extensions
RUN docker-php-ext-configure sqlite3 && \
    docker-php-ext-install \
    pdo \
    pdo_sqlite \
    sqlite3

# Configure nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Create directory structure
RUN mkdir -p /var/www/html /run/nginx /run/php

# Copy application code
COPY . /var/www/html
WORKDIR /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/public && \
    # Ensure PHP-FPM and Nginx can write to run directories
    chown -R www-data:www-data /run/nginx /run/php

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 80

# Start supervisor (which manages nginx and php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
