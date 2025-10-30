#!/bin/bash

# Script pour appliquer les migrations et seeders sur Render PostgreSQL
# Utilisation: ./deploy-db.sh

set -e

echo "🚀 Déploiement de la base de données sur Render..."

# Variables de connexion (utilisez vos vraies valeurs)
DB_HOST="dpg-d41p11hr0fns739dc03g-a.oregon-postgres.render.com"
DB_PORT="5432"
DB_NAME="progetlaravel"
DB_USER="progetlaravel_user"
DB_PASSWORD="NY9eVwhCaB836tTyBvCPoWZsj1EDyLxW"

# Exporter le mot de passe pour psql
export PGPASSWORD="$DB_PASSWORD"

echo "📋 Vérification de la connexion à la base de données..."
psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "SELECT version();" --quiet

if [ $? -eq 0 ]; then
    echo "✅ Connexion réussie à la base de données"
else
    echo "❌ Échec de connexion à la base de données"
    exit 1
fi

echo "🛠️ Application des migrations..."

# Créer un fichier temporaire avec les variables d'environnement pour Laravel
cat > .env.production << EOF
APP_NAME="Proget Laravel 2"
APP_ENV=production
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=false
APP_URL=https://your-render-app.onrender.com

DB_CONNECTION=pgsql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASSWORD

CORS_ALLOWED_ORIGINS="https://your-frontend-domain.com"
CORS_SUPPORTS_CREDENTIALS=true
EOF

echo "📦 Installation des dépendances..."
composer install --optimize-autoloader --no-dev --quiet

echo "🔄 Application des migrations..."
php artisan migrate --force

echo "🌱 Exécution des seeders..."
php artisan db:seed --force

echo "🧹 Nettoyage..."
rm .env.production

echo "✅ Déploiement de la base de données terminé avec succès !"
echo ""
echo "📊 Informations de connexion :"
echo "Host: $DB_HOST"
echo "Port: $DB_PORT"
echo "Database: $DB_NAME"
echo "Username: $DB_USER"