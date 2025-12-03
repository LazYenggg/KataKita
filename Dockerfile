FROM php:8.2-apache

# Install ekstensi yang dibutuhkan CI4
RUN apt-get update && apt-get install -y libicu-dev unzip zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli pdo pdo_mysql

# Aktifkan mod_rewrite (agar URL CI4 cantik)
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ubah Document Root Apache ke folder /public milik CI4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Permission fix (agar www-data bisa tulis)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80