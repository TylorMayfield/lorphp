#!/bin/bash

# Get the PORT from environment variable, default to 80 if not set
export PORT=${PORT:-80}


# Update nginx configuration to listen on the correct port for both HTTP and HTTPS
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/conf.d/default.conf
sed -i "s/listen [::]:80;/listen [::]:$PORT;/g" /etc/nginx/conf.d/default.conf
sed -i "s/listen 443 ssl http2;/listen $PORT ssl http2;/g" /etc/nginx/conf.d/default.conf
sed -i "s/listen [::]:443 ssl http2;/listen [::]:$PORT ssl http2;/g" /etc/nginx/conf.d/default.conf

# Start supervisord (which will start nginx and php-fpm)
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
