# Use Ubuntu as the base image
FROM ubuntu:focal

# Set environment variables
# ENV APACHE_DOCUMENT_ROOT /var/www/public

# # Update the package manager and install dependencies
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
    apache2


    ARG DEBIAN_FRONTEND=noninteractive

# # Install PHP 8.1 FPM
# RUN apt-get install -y \
# libapache2-mod-php8.1

# # On Apache: Enable PHP 8.1 FPM
# RUN a2enconf php8.1-fpm
# RUN a2enmod php8.1

# RUN apt-get remove -y php8.3* || true

# ADD ./dataimport.sql /

RUN mkdir -p / /var/www/html/dataimport

ADD ./ /var/www/html/dataimport

ADD ./Apache2/apache2.conf /etc/apache2/

# ADD ../bash_scripts/Entrypoint.sh /

RUN chmod +x /var/www/html/dataimport/bash_scripts/Entrypoint.sh

CMD [ "/usr/bin/bash", "/var/www/html/dataimport/bash_scripts/Entrypoint.sh" ]

RUN chmod 777 -R /var/www/html/dataimport/storage/

EXPOSE 80 3306
