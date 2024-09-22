# Use the official PHP image
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y libzip-dev unzip git

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . .

# Install PHP extensions
RUN docker-php-ext-install zip pdo pdo_mysql

# Install Composer dependencies
RUN composer install

# Expose the port
EXPOSE 9000

CMD ["php-fpm"]
