#!/bin/bash

docker exec -i tero-mysql mysql -u root -psimplepassword tero_db < ./docker/mysql/tero_full.sql
echo "📥 Импорт базы завершён"
