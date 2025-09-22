FROM php:8.1-apache
RUN apt-get update && apt-get install -y libzip-dev zip unzip libonig-dev && docker-php-ext-install pdo_mysql mbstring zip
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY ../src/ ./src/
COPY ../api/ ./api/
COPY ../init_db.sql ./init_db.sql
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
