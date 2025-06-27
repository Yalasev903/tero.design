#!/bin/bash

docker exec tero-mysql sh -c 'exec mysqldump -uroot -psimplepassword --databases tero_db' > ./docker/mysql/tero_full.sql
echo "✅ Экспорт завершён: docker/mysql/tero_full.sql"
