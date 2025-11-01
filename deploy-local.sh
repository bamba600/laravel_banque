#!/bin/bash

# Script de déploiement local pour tester avant Render
# Usage: ./deploy-local.sh

set -e

echo "🚀 Déploiement local de l'API Bancaire Laravel"
echo "=============================================="

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Installez Docker d'abord."
    exit 1
fi

# Vérifier que le fichier .env existe
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env depuis .env.example..."
    cp .env.example .env
    echo "⚠️  IMPORTANT: Configurez votre .env avec vos credentials de base de données"
    echo "Appuyez sur Entrée pour continuer après avoir configuré .env..."
    read
fi

# Vérifier que APP_KEY est défini
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Génération de APP_KEY..."
    php artisan key:generate
fi

# Construire l'image Docker
echo ""
echo "🔨 Construction de l'image Docker..."
docker build -t api-bancaire-laravel .

# Arrêter le conteneur existant s'il existe
echo ""
echo "🛑 Arrêt du conteneur existant (si présent)..."
docker stop api-bancaire-container 2>/dev/null || true
docker rm api-bancaire-container 2>/dev/null || true

# Lire les variables d'environnement depuis .env
echo ""
echo "📋 Chargement des variables d'environnement..."
export $(cat .env | grep -v '^#' | xargs)

# Démarrer le conteneur
echo ""
echo "🚀 Démarrage du conteneur..."
docker run -d \
  --name api-bancaire-container \
  -p 8000:80 \
  -e APP_NAME="$APP_NAME" \
  -e APP_ENV="$APP_ENV" \
  -e APP_DEBUG="$APP_DEBUG" \
  -e APP_KEY="$APP_KEY" \
  -e APP_URL="http://localhost:8000" \
  -e DB_CONNECTION="$DB_CONNECTION" \
  -e DB_HOST="$DB_HOST" \
  -e DB_PORT="$DB_PORT" \
  -e DB_DATABASE="$DB_DATABASE" \
  -e DB_USERNAME="$DB_USERNAME" \
  -e DB_PASSWORD="$DB_PASSWORD" \
  -e LOG_CHANNEL=stderr \
  -e L5_SWAGGER_CONST_HOST="http://localhost:8000" \
  api-bancaire-laravel

# Attendre que le conteneur démarre
echo ""
echo "⏳ Attente du démarrage du conteneur..."
sleep 5

# Afficher les logs
echo ""
echo "📋 Logs du conteneur:"
echo "===================="
docker logs api-bancaire-container

# Vérifier que le conteneur tourne
if docker ps | grep -q api-bancaire-container; then
    echo ""
    echo "✅ Déploiement réussi!"
    echo ""
    echo "🌐 URLs disponibles:"
    echo "   - Page d'accueil: http://localhost:8000"
    echo "   - Documentation Swagger: http://localhost:8000/api/documentation"
    echo "   - JSON Swagger: http://localhost:8000/api/docs"
    echo "   - API Comptes: http://localhost:8000/api/v1/comptes"
    echo ""
    echo "📋 Commandes utiles:"
    echo "   - Voir les logs: docker logs -f api-bancaire-container"
    echo "   - Arrêter: docker stop api-bancaire-container"
    echo "   - Redémarrer: docker restart api-bancaire-container"
    echo "   - Supprimer: docker rm -f api-bancaire-container"
else
    echo ""
    echo "❌ Erreur: Le conteneur n'a pas démarré correctement"
    echo "Vérifiez les logs ci-dessus pour plus de détails"
    exit 1
fi