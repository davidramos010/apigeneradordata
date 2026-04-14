#!/usr/bin/env bash
set -e

echo "[docker-entrypoint] Iniciando setup de Laravel..."

# Fija permisos (reintentos para evitar problemas de permission denied de host)
echo "[docker-entrypoint] Estableciendo permisos de directorio..."
chown -R www-data:www-data /var/www/html || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Si no existe .env, usa plantilla
if [ ! -f .env ]; then
  echo "[docker-entrypoint] Creando .env desde .env.example..."
  if [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

# Forzar APP_KEY si no está definida
if [ -z "${APP_KEY:-}" ]; then
  echo "[docker-entrypoint] Generando APP_KEY..."
  php artisan key:generate --force
fi

# Instala dependencias Composer sólo si faltan
if [ ! -d vendor ] || [ ! -f composer.lock ]; then
  echo "[docker-entrypoint] Instalando dependencias con Composer..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Migraciones opcional (si conexión disponible)
echo "[docker-entrypoint] Intentando migraciones (ignorando si MySQL no disponible)..."
php artisan migrate --force || echo "[docker-entrypoint] Info: Migraciones no ejecutadas (DB no lista, Eloquent reintentará)"

echo "[docker-entrypoint] Ejecutando seeders (opcional)..."
php artisan db:seed --force || echo "[docker-entrypoint] Info: Seeders ignorados"

# Permisos finales de runtime
echo "[docker-entrypoint] Ajustando permisos finales..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Limpiar cache
echo "[docker-entrypoint] Limpiando caches..."
php artisan cache:clear || true
php artisan config:clear || true

echo "[docker-entrypoint] ✓ Setup completado exitosamente"
echo "[docker-entrypoint] Iniciando servidor de desarrollo en puerto 8000..."
echo ""

# Inicia servidor de desarrollo de Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
