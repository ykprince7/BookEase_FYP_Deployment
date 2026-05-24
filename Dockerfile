FROM ubuntu:22.04
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysqli \
    php8.1-pdo \
    libapache2-mod-php8.1 \
    php8.1-zip \
    php8.1-curl \
    php8.1-mbstring \
    unzip \
    curl \
    && apt-get clean \
    && rm -rf /var/www/html/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite php8.1

COPY . /var/www/html

RUN rm -rf /var/www/html/vendor && cd /var/www/html && composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN printf '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex index.php index.html\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf \
    && a2dissite default 2>/dev/null || true \
    && a2ensite 000-default \
    && rm -f /var/www/html/index.html

EXPOSE 80

CMD ["bash", "-c", "a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork 2>/dev/null; apache2ctl -D FOREGROUND"]