# Guide de Test - Proget Laravel 2

## Problèmes résolus

### 1. Configuration de la base de données de test
**Problème** : Les tests utilisaient la base de données PostgreSQL de production sur Render, ce qui était lent et dangereux.

**Solution** : 
- Création d'un fichier `.env.testing` avec SQLite en mémoire
- Mise à jour de `phpunit.xml` pour utiliser SQLite
- Installation de l'extension PHP SQLite

### 2. Configuration de Laravel Passport pour les tests
**Problème** : Passport nécessite des migrations et une configuration spéciale pour fonctionner dans les tests.

**Solution** :
- Création d'un trait `PassportTestCase` pour gérer la configuration Passport
- Installation automatique de Passport dans `setUp()`
- Création d'un client OAuth pour les tests

## Installation de SQLite

Si SQLite n'est pas installé, exécutez :

```bash
sudo apt-get update
sudo apt-get install -y php-sqlite3 php-pdo-sqlite
```

Vérifiez l'installation :

```bash
php -r "echo (extension_loaded('pdo_sqlite') ? 'PDO_SQLite: YES' : 'PDO_SQLite: NO') . PHP_EOL;"
```

## Exécution des tests

### Tous les tests
```bash
php artisan test
```

### Tests d'authentification uniquement
```bash
php artisan test tests/Feature/AuthTest.php
```

### Test spécifique
```bash
php artisan test --filter user_can_login_with_valid_credentials
```

### Avec couverture de code
```bash
php artisan test --coverage
```

## Structure des tests

### AuthTest.php
Tests d'authentification OAuth avec Passport :
- ✅ `user_can_login_with_valid_credentials` - Connexion avec identifiants valides
- ✅ `user_cannot_login_with_invalid_credentials` - Rejet des identifiants invalides
- ✅ `user_can_refresh_token` - Rafraîchissement du token d'accès

### PassportTestCase.php (Trait)
Trait réutilisable pour configurer Passport dans les tests :
- Installation automatique de Passport
- Création d'un client OAuth password grant
- Méthode helper pour récupérer le client OAuth

## Bonnes pratiques

1. **Toujours utiliser RefreshDatabase** : Garantit une base de données propre pour chaque test
2. **Utiliser des factories** : Créer des données de test avec `User::factory()->create()`
3. **Tester les cas limites** : Identifiants invalides, tokens expirés, etc.
4. **Assertions claires** : Utiliser `assertStatus()`, `assertJsonStructure()`, etc.

## Dépannage

### Les tests sont lents
- Vérifiez que vous utilisez SQLite en mémoire (`:memory:`)
- Vérifiez que `BCRYPT_ROUNDS=4` dans phpunit.xml

### Erreur "Client OAuth invalide"
- Vérifiez que `passport:install` s'exécute dans `setUp()`
- Vérifiez que le client est créé avec `password_client => true`

### Erreur de connexion à la base de données
- Vérifiez que SQLite est installé
- Vérifiez que `DB_CONNECTION=sqlite` dans phpunit.xml
- Vérifiez que `.env.testing` existe

## Fichiers de configuration

### .env.testing
Fichier d'environnement spécifique aux tests avec SQLite en mémoire.

### phpunit.xml
Configuration PHPUnit avec variables d'environnement pour les tests.

### tests/PassportTestCase.php
Trait pour simplifier la configuration de Passport dans les tests.