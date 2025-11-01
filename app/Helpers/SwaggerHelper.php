<?php

if (!function_exists('custom_swagger_asset')) {
    function custom_swagger_asset($asset) {
        if (app()->environment('production')) {
            return "https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.10.3/{$asset}";
        }

        // En développement, utiliser les assets locaux directement
        return asset("vendor/l5-swagger/{$asset}");
    }
}