#!/bin/bash

echo "🌐 Установка npm-зависимостей..."
npm install || echo "⚠️ Ошибка npm install"

echo "🛠 Сборка фронтенда (npm run build)..."
npm run build || echo "⚠️ Ошибка сборки frontend"

echo "🔗 Создание симлинков..."
php artisan storage:link || true

echo "⚙️ Кешируем конфигурации..."
php artisan config:cache
php artisan route:cache

echo "📜 Laravel лог ошибок:"
tail -n 50 storage/logs/laravel.log || echo "❗️ Лог пока не создан"

echo "🚀 Запуск supervisord (nginx + php-fpm)..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
