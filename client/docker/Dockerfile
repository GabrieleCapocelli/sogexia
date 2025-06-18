FROM php:8.3-apache

# Installa Composer e Guzzle
RUN apt-get update && apt-get install -y unzip git zip \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Abilita mod_rewrite
RUN a2enmod rewrite

# Imposta DocumentRoot
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Copia codice e installa dipendenze
COPY ./client /var/www/html
WORKDIR /var/www/html
RUN composer install

# Permessi
RUN chown -R www-data:www-data /var/www/html
