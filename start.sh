#!/bin/bash

echo "🚀 Старт Docker Laravel-проекта"
docker-compose up -d --build

echo "⏳ Ожидание запуска MySQL..."
until docker exec tero-mysql mysqladmin ping -u root -psimplepassword --silent; do
  echo "🔄 Ждём MySQL..."
  sleep 2
done

# ДОП. задержка перед импортом
sleep 5

echo "🛠 Установка зависимостей..."
docker exec tero-app composer install --no-interaction --prefer-dist

echo "🔑 Генерация ключа..."
docker exec tero-app php artisan key:generate

echo "📥 Импорт полной БД (структура + данные)..."
docker exec -i tero-mysql mysql -u root -psimplepassword tero_db < ./docker/mysql/tero_full.sql

echo "🌱 Выполнение сидеров (без миграций)..."
docker exec tero-app php artisan db:seed || echo "⚠️ Ошибка в сидерах — проверь seed-классы"

echo "✅ Готово: http://localhost:8000"
