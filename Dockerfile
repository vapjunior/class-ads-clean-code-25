# Base image with PHP and Apache
FROM php:8.2-apache

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
        zip \
        libpng-dev \
        libjpeg-dev \
        libonig-dev \
        libxml2-dev \
        libpq-dev \
        && docker-php-ext-install pdo_mysql mysqli zip gd

# Enable Apache mod_rewrite (common for many PHP frameworks)
RUN a2enmod rewrite

# Copy custom Apache virtual host config if exists
# Assumes you have something like /apache_conf/000-default.conf
COPY ./apache_conf/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application code (or let volume in docker-compose handle it)
# Uncomment below if you want to build the app inside image
# COPY ./app /var/www/html

# Expose Apache port (optional since docker-compose maps it)
EXPOSE 80