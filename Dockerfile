FROM php:8.2-cli-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Ensure storage directory exists and is writable
RUN mkdir -p storage && \
    chown -R www-data:www-data storage && \
    chmod -R 775 storage

# Expose Sevella's expected port
EXPOSE 8080

# Start PHP built-in server on port 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
