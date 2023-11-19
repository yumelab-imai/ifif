FROM php:8.2-fpm

# ユーザーとグループのIDを指定
# ※ Linuxの一般ユーザーのデフォルトのID
ARG UID=1000
ARG GID=1000

# www-data ユーザーのUIDとGIDを変更
RUN usermod -u $UID www-data && groupmod -g $GID www-data
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Composer install
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# install Node.js
COPY --from=node:latest /usr/local/bin /usr/local/bin
COPY --from=node:latest /usr/local/lib /usr/local/lib

RUN apt-get update && \
    apt-get -y install \
    git \
    zip \
    unzip \
    vim \
    && docker-php-ext-install pdo_mysql bcmath

WORKDIR /var/www/html
