#!/bin/bash

# Script de vérification du déploiement
# Usage: ./check-deployment.sh [URL]
# Exemple: ./check-deployment.sh https://proget-laravel-api.onrender.com

URL="${1:-http://localhost:8000}"

echo "🔍 Vérification du déploiement de l'API"
echo "========================================"
echo "URL: $URL"
echo ""

# Fonction pour tester un endpoint
test_endpoint() {
    local endpoint=$1
    local description=$2
    
    echo -n "Testing $description... "
    
    response=$(curl -s -o /dev/null -w "%{http_code}" "$URL$endpoint" 2>/dev/null)
    
    if [ "$response" = "200" ]; then
        echo "✅ OK (HTTP $response)"
        return 0
    else
        echo "❌ FAILED (HTTP $response)"
        return 1
    fi
}

# Tests
echo "📋 Tests des endpoints:"
echo ""

test_endpoint "/" "Page d'accueil"
test_endpoint "/api/documentation" "Documentation Swagger"
test_endpoint "/api/docs" "JSON Swagger"
test_endpoint "/api/v1/comptes" "API Comptes"

echo ""
echo "🔍 Vérification détaillée de la page d'accueil:"
echo "================================================"

response=$(curl -s "$URL/")
if echo "$response" | grep -q "API Bancaire"; then
    echo "✅ La page d'accueil contient le titre attendu"
else
    echo "❌ La page d'accueil ne contient pas le titre attendu"
    echo "Réponse reçue:"
    echo "$response" | head -20
fi

echo ""
echo "🔍 Vérification de la documentation Swagger:"
echo "============================================="

swagger_json=$(curl -s "$URL/api/docs")
if echo "$swagger_json" | grep -q "openapi"; then
    echo "✅ Le fichier Swagger JSON est valide"
    echo "   Version OpenAPI: $(echo "$swagger_json" | grep -o '"openapi":"[^"]*"' | cut -d'"' -f4)"
    echo "   Titre: $(echo "$swagger_json" | grep -o '"title":"[^"]*"' | head -1 | cut -d'"' -f4)"
else
    echo "❌ Le fichier Swagger JSON est invalide ou manquant"
fi

echo ""
echo "✅ Vérification terminée!"