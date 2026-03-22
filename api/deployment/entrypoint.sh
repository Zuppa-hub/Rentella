#!/bin/sh
set -e

refresh_keycloak_public_key() {
  ENV_FILE="/var/www/html/.env"
  [ -f "$ENV_FILE" ] || return 0

  KEYCLOAK_BASE_URL=$(grep '^KEYCLOAK_BASE_URL=' "$ENV_FILE" | head -n1 | cut -d'=' -f2-)
  KEYCLOAK_REALM=$(grep '^KEYCLOAK_REALM=' "$ENV_FILE" | head -n1 | cut -d'=' -f2-)

  if [ -z "$KEYCLOAK_BASE_URL" ] || [ -z "$KEYCLOAK_REALM" ]; then
    echo "[entrypoint] Keycloak URL/realm not configured in .env, skipping key refresh."
    return 0
  fi

  REALM_ENDPOINT="$KEYCLOAK_BASE_URL/realms/$KEYCLOAK_REALM"
  echo "[entrypoint] Fetching Keycloak realm public key from $REALM_ENDPOINT ..."

  REALM_PUBLIC_KEY=""
  for i in $(seq 1 40); do
    REALM_PUBLIC_KEY=$(php -r '
      $url = $argv[1] ?? "";
      if (!$url) { exit(1); }
      $json = @file_get_contents($url);
      if ($json === false) { exit(1); }
      $data = json_decode($json, true);
      if (!is_array($data) || empty($data["public_key"])) { exit(1); }
      echo $data["public_key"];
    ' "$REALM_ENDPOINT") || REALM_PUBLIC_KEY=""

    if [ -n "$REALM_PUBLIC_KEY" ]; then
      break
    fi

    echo "[entrypoint] Keycloak not ready yet (attempt $i), retrying..."
    sleep 2
  done

  if [ -z "$REALM_PUBLIC_KEY" ]; then
    echo "[entrypoint] Unable to fetch Keycloak public key after retries."
    return 1
  fi

  sed -i "s|^KEYCLOAK_REALM_PUBLIC_KEY=.*|KEYCLOAK_REALM_PUBLIC_KEY=$REALM_PUBLIC_KEY|" "$ENV_FILE"
  echo "[entrypoint] Keycloak public key refreshed in .env"

  php /var/www/html/artisan config:clear >/dev/null 2>&1 || true
}

refresh_keycloak_public_key || {
  echo "[entrypoint] Fatal: Keycloak public key refresh failed. Exiting."
  exit 1
}

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
