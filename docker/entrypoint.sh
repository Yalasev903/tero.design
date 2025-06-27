#!/bin/bash

echo "🔗 Создание симлинков..."
php artisan storage:link || true

echo "📦 Кеширование конфигурации и маршрутов..."
php artisan config:cache || true
php artisan route:cache || true

echo "📜 Последние строки из лога Laravel:"
tail -n 50 storage/logs/laravel.log || echo "❗️ Лог пока не создан"

echo "🚀 Запуск supervisord (nginx + php-fpm)..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
