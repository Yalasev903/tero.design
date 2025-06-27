FROM php:8.4-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    nginx supervisor unzip curl git zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем проект
COPY . .

# Конфиг nginx и supervisor
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Даем права
RUN chmod -R 755 /var/www && chown -R www-data:www-data /var/www

# Composer install
RUN composer install --no-interaction --prefer-dist

# Ключ
RUN php artisan key:generate

# Удалить .env, Railway сам задаёт переменные окружения
RUN rm -f .env

# Порт для Railway
ENV PORT=8080

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
