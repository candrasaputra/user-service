# Use an official PHP runtime as a parent image
FROM php:8.3-fpm

USER root

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libpq-dev libzip-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Set the working directory
WORKDIR /var/www

# Copy the composer.lock and composer.json files
COPY composer.lock composer.json /var/www/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code
COPY . /var/www

# Set permissions for storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Generate the application autoloader
RUN composer dump-autoload --optimize

# Expose port 9000
EXPOSE 9000

# Start the PHP FastCGI Process Manager
CMD ["php-fpm"]
