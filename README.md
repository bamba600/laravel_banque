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

#### 3. Variables d'environnement ⚠️ IMPORTANT

**VOUS DEVEZ** configurer ces variables dans Render pour que l'application fonctionne :

```yaml
# Application (OBLIGATOIRE)
APP_NAME: "Proget Laravel 2"
APP_ENV: production
APP_DEBUG: false
APP_KEY: base64:8KzP3vJ9mN2qR5tY7wX0zA1bC4dE6fG8hI9jK0lM1nO=  # ⚠️ À CHANGER !
APP_URL: https://proget-laravel-api.onrender.com

# Logs
LOG_CHANNEL: stderr
LOG_LEVEL: error

# Base de données (déjà configurées)
DB_CONNECTION: pgsql
DB_HOST: dpg-d41p11hr0fns739dc03g-a.oregon-postgres.render.com
DB_PORT: 5432
DB_DATABASE: progetlaravel
DB_USERNAME: progetlaravel_user
DB_PASSWORD: NY9eVwhCaB836tTyBvCPoWZsj1EDyLxW

# Swagger Documentation
L5_SWAGGER_CONST_HOST: https://proget-laravel-api.onrender.com
L5_SWAGGER_GENERATE_ALWAYS: false
L5_SWAGGER_USE_ABSOLUTE_PATH: true

# CORS (optionnel)
CORS_ALLOWED_ORIGINS: "http://localhost:3000,http://127.0.0.1:3000"
CORS_SUPPORTS_CREDENTIALS: true
```

**🔑 Pour générer une vraie APP_KEY :**
```bash
# Localement
php artisan key:generate --show

# Ou utilisez temporairement celle ci-dessus et changez-la après le premier déploiement
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

### ❌ Erreur 500 - "ERROR: APP_KEY is not set!"
**Cause** : La variable `APP_KEY` n'est pas configurée dans Render

**Solution** :
1. Allez dans Render Dashboard → Votre service → Environment
2. Ajoutez `APP_KEY` avec une valeur générée (voir section Variables d'environnement)
3. Sauvegardez et redéployez

### ❌ Erreur 500 - Page blanche
**Causes possibles** :
- Fichier `welcome.blade.php` manquant (✅ maintenant créé)
- Problème de permissions sur `storage/`
- Erreur de configuration

**Solution** :
1. Vérifiez les logs Render (onglet Logs)
2. Activez temporairement `APP_DEBUG=true` pour voir les erreurs détaillées
3. Vérifiez que toutes les variables d'environnement sont définies

### ❌ Documentation Swagger ne s'affiche pas
**Solution** :
1. Vérifiez que `L5_SWAGGER_CONST_HOST` est défini
2. Accédez à `/api/docs` pour vérifier que le JSON est généré
3. Vérifiez les logs pour les erreurs de génération Swagger

### ❌ Problème de CORS
**Solution** :
- Ajoutez votre domaine frontend dans `CORS_ALLOWED_ORIGINS`
- Format : `"https://votre-frontend.onrender.com,http://localhost:3000"`
- Redéployez l'application

### ❌ Base de données inaccessible
**Solution** :
- Vérifiez les credentials PostgreSQL dans les variables d'environnement
- S'assurer que la DB Render est active (pas en hibernation)
- Testez la connexion : les logs montreront "Waiting for database..."

## 📞 Support et Debugging

### Checklist de déploiement ✅

- [ ] `APP_KEY` est défini dans Render
- [ ] Toutes les variables d'environnement sont configurées
- [ ] La base de données PostgreSQL est active
- [ ] Le fichier `welcome.blade.php` existe
- [ ] Les logs Render ne montrent pas d'erreurs critiques

### Pour déboguer :

1. **Vérifier les logs Render** (onglet Logs dans le dashboard)
2. **Tester localement** avec Docker :
   ```bash
   docker build -t api-test .
   docker run -p 8000:80 -e APP_KEY=base64:test... api-test
   ```
3. **Consulter la documentation Swagger** : `/api/documentation`
4. **Tester les endpoints** avec curl ou Postman

### Fichiers de référence

- `RENDER_DEPLOYMENT.md` - Guide détaillé de déploiement
- `.env.example` - Template des variables d'environnement
- `Dockerfile` - Configuration Docker avec script de démarrage amélioré

## 🔄 Mises à jour

Le déploiement est automatique via GitHub. Chaque push sur la branche `main` déclenche un redéploiement.

---

**🎉 Déploiement réussi ! Votre API bancaire est maintenant opérationnelle sur Render.**
