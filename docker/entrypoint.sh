#!/bin/bash

echo "📦 Выполняем миграции..."
php artisan migrate --force || echo "⚠️ Ошибка миграции"

echo "🌱 Выполняем сиды..."
php artisan db:seed || echo "⚠️ Ошибка сидеров"

echo "🔗 Создание симлинков..."
php artisan storage:link || true

echo "⚙️ Кешируем конфигурации..."
php artisan config:cache
php artisan route:cache

# Запускаем supervisor (nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisord.conf

echo "📜 Laravel лог ошибок:"
tail -n 50 storage/logs/laravel.log || echo "❗️ Лог пока не создан"
