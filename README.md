# 🚀 API Bancaire Laravel - Déploiement sur Render

## 📋 Vue d'ensemble

Cette API Laravel fournit une gestion complète des comptes bancaires avec authentification, transactions et blocages automatiques. Déployée sur Render avec Docker et PostgreSQL.

## 🌐 URLs de production

- **API Base**: `https://proget-laravel-api.onrender.com`
- **Documentation Swagger**: `https://proget-laravel-api.onrender.com/api/documentation`

## 🛠️ Technologies utilisées

- **Laravel 10** - Framework PHP
- **PostgreSQL** - Base de données
- **Docker** - Containerisation
- **Render** - Plateforme de déploiement
- **L5-Swagger** - Documentation API

## 📚 Endpoints API

### Comptes bancaires

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/v1/comptes` | Lister tous les comptes (avec pagination et filtres) |
| GET | `/api/v1/comptes/{numero}` | Détails d'un compte par numéro |
| GET | `/api/v1/comptes/client/{telephone}` | Comptes d'un client par téléphone |
| POST | `/api/v1/comptes/{id}/bloquer` | Bloquer un compte épargne |

### Paramètres de requête (GET /api/v1/comptes)

- `page`: numéro de page (défaut: 1)
- `limit`: éléments par page (défaut: 10, max: 100)
- `type`: filtre par type (`epargne`, `courant`)
- `statut`: filtre par statut (`actif`, `bloque`)
- `sort`: champ de tri (`dateCreation`, `numero`, `solde`)
- `order`: ordre (`asc`, `desc`)

## 🚀 Déploiement

### Prérequis

1. **Repository GitHub** avec le code source
2. **Compte Render** (gratuit disponible)
3. **Base de données PostgreSQL** sur Render

### Étapes de déploiement

#### 1. Créer la base de données PostgreSQL sur Render

1. Aller sur [dashboard.render.com](https://dashboard.render.com)
2. Cliquer "New" → "PostgreSQL"
3. Configurer :
   - **Name**: `proget-laravel-db`
   - **Plan**: Free
4. Noter les informations de connexion

#### 2. Déployer l'application

1. Dans Render, cliquer "New" → "Web Service"
2. Connecter votre repository GitHub
3. Configurer :
   - **Name**: `proget-laravel-api`
   - **Runtime**: Docker
   - **Dockerfile Path**: `./Dockerfile`
4. Ajouter les variables d'environnement (voir section suivante)

#### 3. Variables d'environnement

```yaml
# Application
APP_NAME: "Proget Laravel 2"
APP_ENV: production
APP_DEBUG: false
APP_KEY: # Généré automatiquement

# Base de données
DB_CONNECTION: pgsql
DB_HOST: dpg-xxxxxxxxxxxxxxxxxx.oregon-postgres.render.com
DB_PORT: 5432
DB_DATABASE: progetlaravel
DB_USERNAME: progetlaravel_user
DB_PASSWORD: votre_mot_de_passe_db

# CORS
CORS_ALLOWED_ORIGINS: "https://votre-frontend.onrender.com"
CORS_SUPPORTS_CREDENTIALS: true
```

## 🔧 Configuration

### Base de données

L'application utilise PostgreSQL avec les tables suivantes :
- `users` - Utilisateurs/clients
- `comptes` - Comptes bancaires
- `transactions` - Transactions
- `oauth_access_tokens` - Tokens d'authentification

### Migrations et seeders

Les migrations sont exécutées automatiquement au démarrage. Les seeders créent :
- 4 administrateurs
- 27 clients (22 actifs, 3 inactifs, 2 non vérifiés)

## 📖 Utilisation de l'API

### Exemples de requêtes

#### Lister les comptes
```bash
curl "https://proget-laravel-api.onrender.com/api/v1/comptes?page=1&limit=5&type=epargne"
```

#### Détails d'un compte
```bash
curl "https://proget-laravel-api.onrender.com/api/v1/comptes/CPT0000000000001"
```

#### Comptes d'un client
```bash
curl "https://proget-laravel-api.onrender.com/api/v1/comptes/client/+221771234567"
```

## 🔍 Monitoring et débogage

### Logs Render
- Aller dans votre service → onglet "Logs"
- Voir les logs en temps réel

### Tests locaux
```bash
# Avec Docker Compose
docker-compose up --build

# Tests unitaires
php artisan test
```

## 🐛 Résolution des problèmes

### Erreur 500
- Vérifier les logs Render
- Contrôler la connexion DB
- Vérifier les variables d'environnement

### Problème de CORS
- Ajouter votre domaine frontend dans `CORS_ALLOWED_ORIGINS`
- Redéployer l'application

### Base de données inaccessible
- Vérifier les credentials PostgreSQL
- S'assurer que la DB est active
- Contrôler les règles de firewall

## 📞 Support

Pour toute question ou problème :
1. Vérifier les logs Render
2. Tester localement avec Docker
3. Consulter la documentation Swagger

## 🔄 Mises à jour

Le déploiement est automatique via GitHub. Chaque push sur la branche `main` déclenche un redéploiement.

---

**🎉 Déploiement réussi ! Votre API bancaire est maintenant opérationnelle sur Render.**
