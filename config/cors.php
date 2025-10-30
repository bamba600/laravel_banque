<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Les chemins pour lesquels CORS doit être appliqué.
    | Nous incluons toutes les routes API v1 et les routes Sanctum.
    |
    */
    'paths' => [
        'api/v1/*',           // Toutes les routes API version 1
        'sanctum/csrf-cookie', // Pour l'authentification Sanctum
        'api/documentation',  // Interface Swagger
        'docs/*'              // Fichiers de documentation JSON
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Méthodes HTTP autorisées pour les requêtes CORS.
    | Pour une API REST, nous autorisons GET, POST, PUT, PATCH, DELETE.
    |
    */
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Origines autorisées pour les requêtes CORS.
    | En développement : localhost et 127.0.0.1
    | En production : domaines spécifiques de l'application bancaire
    |
    */
    'allowed_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:8080,http://127.0.0.1:3000,http://127.0.0.1:8000,http://localhost:8000,https://banque.example.com'))),

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns pour les origines autorisées (expressions régulières).
    | Utile pour autoriser des sous-domaines.
    |
    */
    'allowed_origins_patterns' => [
        // '/^https?:\/\/(.*\.)?banque\.example\.com$/'
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Headers autorisés dans les requêtes CORS.
    | Nous incluons les headers standards plus Authorization et X-Requested-With.
    |
    */
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-API-Key',
        'Origin',
        'Referer',
        'User-Agent'
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers exposés au client JavaScript.
    | Utile pour transmettre des informations supplémentaires.
    |
    */
    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset'
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Max Age
    |--------------------------------------------------------------------------
    |
    | Durée de cache des requêtes preflight CORS (en secondes).
    | 3600 = 1 heure de cache pour éviter les requêtes OPTIONS répétées.
    |
    */
    'max_age' => 3600,

    /*
    |--------------------------------------------------------------------------
    | CORS Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Autorise l'envoi de credentials (cookies, headers d'authentification).
    | Nécessaire pour les APIs avec authentification par token ou session.
    |
    */
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', true),

];
