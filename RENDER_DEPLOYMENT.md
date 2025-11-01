# 🚀 Guide de Déploiement sur Render

## ⚠️ Problème Résolu

Les erreurs 500 que vous rencontriez étaient causées par :
1. ❌ Variable `APP_KEY` manquante
2. ❌ Fichier `welcome.blade.php` manquant (maintenant créé)
3. ❌ Configuration Swagger incomplète

## 📋 Variables d'Environnement Requises sur Render

Allez dans votre dashboard Render → Votre service → **Environment** et ajoutez ces variables :

### Variables Essentielles

```env
APP_NAME=Proget Laravel 2
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:VOTRE_CLE_ICI
APP_URL=https://proget-laravel-api.onrender.com
```

### Base de Données (déjà configurées)

```env
DB_CONNECTION=pgsql
DB_HOST=dpg-d41p11hr0fns739dc03g-a.oregon-postgres.render.com
DB_PORT=5432
DB_DATABASE=progetlaravel
DB_USERNAME=progetlaravel_user
DB_PASSWORD=NY9eVwhCaB836tTyBvCPoWZsj1EDyLxW
```

### Logs

```env
LOG_CHANNEL=stderr
LOG_LEVEL=error
```

### Swagger/Documentation

```env
L5_SWAGGER_GENERATE_ALWAYS=false
L5_SWAGGER_CONST_HOST=https://proget-laravel-api.onrender.com
L5_SWAGGER_USE_ABSOLUTE_PATH=true
ASSET_URL=https://proget-laravel-api.onrender.com
```

> **Note:** `ASSET_URL` force tous les assets (CSS, JS, images) à être chargés via HTTPS, résolvant ainsi les erreurs de contenu mixte (mixed content).

### CORS (optionnel)

```env
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
CORS_SUPPORTS_CREDENTIALS=true
```

## 🔑 Générer APP_KEY

### Option 1 : Localement
```bash
php artisan key:generate --show
```

### Option 2 : Utiliser cette clé temporaire
```
base64:8KzP3vJ9mN2qR5tY7wX0zA1bC4dE6fG8hI9jK0lM1nO=
```

⚠️ **IMPORTANT** : Changez cette clé après le premier déploiement !

## 🎯 Étapes de Déploiement

1. **Ajouter toutes les variables d'environnement** dans Render
2. **Commit et push** les changements :
   ```bash
   git add .
   git commit -m "Fix: Add welcome page and update environment config"
   git push
   ```
3. **Render redéploiera automatiquement**
4. **Vérifier** :
   - Page d'accueil : https://proget-laravel-api.onrender.com
   - Documentation : https://proget-laravel-api.onrender.com/api/documentation
   - JSON Swagger : https://proget-laravel-api.onrender.com/api/docs

## ✅ URLs Disponibles

| URL | Description |
|-----|-------------|
| `/` | Page d'accueil de l'API |
| `/api/documentation` | Interface Swagger UI |
| `/api/docs` | Fichier JSON Swagger |
| `/api/v1/comptes` | Liste des comptes |
| `/api/v1/comptes/{numero}` | Détails d'un compte |
| `/api/v1/comptes/client/{telephone}` | Comptes d'un client |
| `/api/v1/comptes/{compteId}/bloquer` | Bloquer un compte |

## 🐛 Debugging

Si vous rencontrez encore des erreurs :

1. **Activer le mode debug temporairement** :
   ```env
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```

2. **Vérifier les logs Render** dans le dashboard

3. **Tester localement** :
   ```bash
   php artisan serve
   ```

## 📝 Notes

- Le Dockerfile génère automatiquement la documentation Swagger au démarrage
- Les migrations s'exécutent automatiquement
- Apache est configuré pour servir depuis `/var/www/html/public`
- Les assets Swagger sont copiés dans `public/vendor/l5-swagger/`

## 🔒 Sécurité

Pour la production, assurez-vous de :
- [ ] Changer `APP_DEBUG=false`
- [ ] Utiliser une vraie `APP_KEY` unique
- [ ] Configurer HTTPS (déjà fait par Render)
- [ ] Limiter les origines CORS si nécessaire