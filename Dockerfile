FROM php:8.4-fpm

# Установка Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs


RUN apt-get update && apt-get install -y \
    nginx supervisor unzip curl git zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring gd zip


# Установка composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Конфигурация nginx и supervisord
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Включение сайта
RUN mkdir -p /etc/nginx/sites-enabled && \
    rm -f /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Права и автолоад
RUN chmod -R 755 /var/www && chown -R www-data:www-data /var/www
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Копируем .env только если он отсутствует
RUN test -f .env || cp .env.example .env

RUN npm install && npm run build

# Генерация ключа
RUN php artisan key:generate --ansi

# Копируем entrypoint
COPY ./docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Открываем порт
ENV PORT=8080
EXPOSE 8080

# Финальный запуск
CMD ["sh", "/var/www/docker/entrypoint.sh"]
