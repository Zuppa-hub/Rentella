#!/bin/sh
set -e

if [ -f /var/www/html/.env ]; then
  echo "[entrypoint] Running migrations and seeders..."
  set +e
  for i in $(seq 1 30); do
    php /var/www/html/artisan migrate --seed --force && break
    echo "[entrypoint] Database not ready yet (attempt $i). Retrying..."
    sleep 2
  done
  set -e
fi

exec apache2-foreground
