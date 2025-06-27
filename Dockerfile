# Используем официальный образ PHP 8.4 с FPM
FROM php:8.4-fpm

# Установка Node.js 20 и системных зависимостей
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get update && apt-get install -y \
    nodejs nginx supervisor unzip curl git zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring gd zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копируем все файлы проекта
COPY . .

# Копируем конфигурации nginx и supervisor
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Создаём симлинк на активный конфиг
RUN mkdir -p /etc/nginx/sites-enabled && \
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Устанавливаем зависимости PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Установка прав
RUN chmod -R 755 /var/www && chown -R www-data:www-data /var/www

# Копируем .env только если он отсутствует
RUN test -f .env || cp .env.example .env

# Устанавливаем зависимости и билдим фронтенд
ENV NODE_ENV=production
RUN npm install && npm run build

# Генерация ключа приложения Laravel
RUN php artisan key:generate --ansi

# Кешируем конфиги и роуты
RUN php artisan config:cache && \
    php artisan route:cache

# Копируем скрипт запуска
COPY ./docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Открываем порт для nginx
ENV PORT=8080
EXPOSE 8080

# Запускаем Laravel + nginx + php-fpm через supervisord
CMD ["sh", "/var/www/docker/entrypoint.sh"]
