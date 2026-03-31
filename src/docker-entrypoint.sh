#!/usr/bin/env bash
set -e

# Fija permisos (reintentos para evitar problemas de permission denied de host)
chown -R www-data:www-data /var/www/html || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Si no existe .env, usa plantilla
if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

# Forzar APP_KEY si no está definida
if [ -n "${APP_KEY:-}" ]; then
  true
else
  php artisan key:generate --force
fi

# Instala dependencias Composer sólo si faltan
if [ ! -d vendor ] || [ ! -f composer.lock ]; then
  composer install --no-interaction --prefer-dist --no-scripts --optimize-autoloader
fi

# Espera por MySQL a que esté listo
MAX_TRIES=30
TRY=0
until mysqladmin ping -h "${DB_HOST:-mysql}" -P "${DB_PORT:-3306}" -u "${DB_USERNAME:-laravel}" -p"${DB_PASSWORD:-secret}" --silent; do
  TRY=$((TRY + 1))
  echo "[docker-entrypoint] Esperando MySQL... ($TRY/$MAX_TRIES)"
  sleep 2
  if [ "$TRY" -ge "$MAX_TRIES" ]; then
    echo "[docker-entrypoint] MySQL no está disponible después de $MAX_TRIES intentos. Abortando."
    exit 1
  fi
done

# Migraciones y seeders en cada arranque (no falla si ya existe)
php artisan migrate --force || true
php artisan db:seed --force || true

# Permisos finales de runtime
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Inicia servidor de desarrollo de Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
