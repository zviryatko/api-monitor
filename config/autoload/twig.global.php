<?php

return [
    'twig' => [
        'autoescape' => 'html', // Auto-escaping strategy [html|js|css|url|false]
        'cache_dir' => 'data/cache',
        'assets_url' => 'base URL for assets',
        'assets_version' => 'base version for assets',
        'extensions' => [
            new Twig_Extensions_Extension_Text,
        ],
        'optimizations' => -1, // -1: Enable all (default), 0: disable optimizations
    ],
];
