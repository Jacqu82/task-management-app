#!/bin/sh
set -e

cd /web/backend

echo "🚀 Starting backend container..."

if [ ! -d "vendor" ]; then
  echo "📦 Installing composer dependencies..."
  composer install --no-interaction --prefer-dist
else
  echo "📦 Composer dependencies already installed"
fi

echo "⏳ Waiting for database..."
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  sleep 1
done

echo "🗄 Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

mkdir -p config/jwt
chown -R $(id -u):$(id -g) config/jwt

if [ ! -f config/jwt/private.pem ] || [ ! -f config/jwt/public.pem ]; then
    echo "🔑 Generating JWT keypair..."
    php bin/console lexik:jwt:generate-keypair --overwrite
else
    echo "🔑 JWT keypair already exists"
fi

echo "✅ Backend ready"

exec "$@"