FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor unzip curl git zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

RUN mkdir -p /etc/nginx/sites-enabled && \
    rm -f /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

RUN chmod -R 755 /var/www && chown -R www-data:www-data /var/www
RUN composer install --no-interaction --prefer-dist

RUN cp .env.example .env
RUN php artisan key:generate
RUN rm -f .env

RUN php artisan db:seed || echo "⚠️ Ошибка в сидерах — проверь seed-классы"

ENV PORT=8080
EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
