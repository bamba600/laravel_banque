#!/bin/bash

# Script pour exécuter les tests avec la bonne configuration

echo "🧪 Préparation de l'environnement de test..."

# Vérifier si SQLite est installé
if php -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);" 2>/dev/null; then
    echo "✅ SQLite est installé"
    export DB_CONNECTION=sqlite
    export DB_DATABASE=:memory:
else
    echo "⚠️  SQLite n'est pas installé. Installation en cours..."
    echo "Vous pouvez aussi utiliser PostgreSQL local pour les tests."
    echo "Voulez-vous continuer avec PostgreSQL? (y/n)"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        export DB_CONNECTION=pgsql
        export DB_HOST=localhost
        export DB_PORT=5432
        export DB_DATABASE=progetlaravel_test
        export DB_USERNAME=postgres
        export DB_PASSWORD=
        echo "📝 Utilisation de PostgreSQL local"
        echo "⚠️  Assurez-vous d'avoir créé la base 'progetlaravel_test'"
    else
        echo "❌ Installation de SQLite requise. Exécutez:"
        echo "   sudo apt-get install -y php-sqlite3 php-pdo-sqlite"
        exit 1
    fi
fi

# Exécuter les tests
echo ""
echo "🚀 Exécution des tests..."
php artisan test "$@"