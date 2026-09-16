#!/bin/sh
set -e

# Directorios que Laravel necesita poder escribir (por si el volumen/build los recreó sin permisos)
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Enlace público de storage (idempotente: no falla si ya existe)
php artisan storage:link || true

# Migraciones: seguras de correr en cada arranque, Laravel omite las ya aplicadas.
# --isolated usa un lock atómico (vía Redis, CACHE_DRIVER=redis) para que, si
# Railway (u otra plataforma) llega a tener dos contenedores corriendo a la vez
# durante un rolling deploy, solo uno migre por vez en lugar de chocar entre sí
# con errores de "table already exists".
#
# RESET_DB_ON_BOOT=true fuerza una migración limpia (borra todo) — usarlo solo
# para recuperar una base en estado inconsistente, nunca dejarlo activo de
# forma permanente.
if [ "$RESET_DB_ON_BOOT" = "true" ]; then
    php artisan migrate:fresh --force
else
    php artisan migrate --force --isolated
fi

# Sembrar roles/permisos/usuario admin solo la primera vez (tabla usuarios vacía).
# En reinicios posteriores ya hay datos y correr los seeders de nuevo rompería
# por violación de unicidad (email), así que no se ejecuta si ya hay usuarios.
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ]; then
    php artisan db:seed --force
fi

# Cachear config/rutas/vistas con las variables de entorno reales del contenedor
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
