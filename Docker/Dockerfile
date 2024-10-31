# Use Ubuntu as the base image
FROM ubuntu:latest

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update the package manager and install dependencies
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update && apt-get install -y \
    php8.1 \
    php8.1-cli \
    php8.1-fpm \
    php8.1-mbstring \
    php8.1-xml \
    php8.1-curl \
    php8.1-zip \
    php8.1-mysql \
    php8.1-bcmath \
    php8.1-tokenizer \
    php8.1-intl \
    php8.1-gd \
    php8.1-dom \
    apache2 \
    libapache2-mod-php8.1

# On Apache: Enable PHP 8.1 FPM
RUN a2enconf php8.1-fpm
RUN a2enmod php8.1

RUN apt-get remove -y php8.3* || true

# Enable Apache modules and set up the document root for Laravel
RUN a2enmod rewrite && \
    sed -ri s!/var/www/html!{$APACHE_DOCUMENT_ROOT}!g /etc/apache2/sites-available/*.conf

# Install Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://getcomposer.org/download/2.2.0/composer.phar -o /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer


# Set the working directory
WORKDIR /var/www/html

# Install Laravel
# RUN composer create-project --prefer-dist laravel/laravel laravel-app

# Move the Laravel app files to the Apache root directory
COPY ../ .

# COPY ./Entrypoint.sh

ENTRYPOINT [ "Docker/Entrypoint.sh" ]
# RUN composer install --no-interaction --optimize-autoloader --prefer-dist

# USER www-data
# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
# chown -R www-data:www-data /var/www/html \
#     && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apachectl", "-D", "FOREGROUND"]
