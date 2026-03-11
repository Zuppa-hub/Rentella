#!/bin/sh
set -e

if [ -f /var/www/html/.env ]; then
  echo "[entrypoint] Running migrations and seeders..."
  MIGRATION_SUCCESS=0
  for i in $(seq 1 30); do
    php /var/www/html/artisan migrate --seed --force && { MIGRATION_SUCCESS=1; break; }
    echo "[entrypoint] Database not ready yet (attempt $i). Retrying..."
    sleep 2
  done
  if [ "$MIGRATION_SUCCESS" -ne 1 ]; then
    echo "[entrypoint] Migrations and seeders failed after multiple attempts. Exiting."
    exit 1
  fi
fi

exec apache2-foreground
