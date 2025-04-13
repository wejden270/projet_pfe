<?php
return [
    /*
    |----------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |----------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Définir les chemins API

    'allowed_methods' => ['*'], // Autoriser toutes les méthodes HTTP (GET, POST, PUT, DELETE, etc.)

    'allowed_origins' => ['*'], // Autoriser toutes les origines pour faciliter le développement. Vous pouvez restreindre à des domaines spécifiques si nécessaire

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Autoriser tous les en-têtes HTTP

    'exposed_headers' => [],

    'max_age' => 0, // Définir le délai de mise en cache des résultats CORS (en secondes). À 0, cela signifie que les résultats ne sont pas mis en cache.

    'supports_credentials' => false, // Définir sur `true` si vous utilisez des cookies ou des sessions avec votre API
];
