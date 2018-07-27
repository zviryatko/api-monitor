<?php

return [
    'templates' => [
        'debug' => true,
        'extension' => 'html.twig',
        'paths' => [['templates']],
    ],
    'twig' => [
        'autoescape' => 'html', // Auto-escaping strategy [html|js|css|url|false]
        'cache_dir' => 'data/cache/twig',
        'assets_url' => '/',
        'assets_version' => '0.1',
        'extensions' => [
            new Twig_Extensions_Extension_Text,
        ],
        'optimizations' => -1, // -1: Enable all (default), 0: disable optimizations
    ],
];
