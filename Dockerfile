FROM php:8.4-fpm

# Установка Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get update && apt-get install -y \
    nodejs nginx supervisor unzip curl git zip \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring gd zip

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем только package.json и package-lock.json
COPY package*.json ./

# Установка NPM зависимостей (laravel-vite-plugin тут!)
RUN npm install

# Копируем остальной проект
COPY . .

# Laravel зависимости
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Vite build
ENV NODE_ENV=production
RUN npm run build && \
    cp public/build/.vite/manifest.json public/build/manifest.json

# Laravel .env и ключ
RUN test -f .env || cp .env.example .env
RUN php artisan key:generate --ansi
RUN php artisan config:cache && php artisan route:cache

# Конфиги nginx/supervisor
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf
RUN mkdir -p /etc/nginx/sites-enabled && \
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Entrypoint
COPY ./docker/entrypoint.sh /var/www/docker/entrypoint.sh
RUN chmod +x /var/www/docker/entrypoint.sh

# Права
RUN chmod -R 755 /var/www && chown -R www-data:www-data /var/www

EXPOSE 8080
CMD ["sh", "/var/www/docker/entrypoint.sh"]
