FROM php:8.2-apache

RUN sed -i 's/^/#/' /etc/apache2/mods-enabled/mpm_event.load 2>/dev/null || true
RUN sed -i 's/^/#/' /etc/apache2/mods-enabled/mpm_event.conf 2>/dev/null || true

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && apt-get clean

RUN a2enmod rewrite

RUN rm -f /var/www/html/index.html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80