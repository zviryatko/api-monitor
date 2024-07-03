<?php

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'proxy_dir' => './data/cache/doctrine-proxy/',
                'metadata_cache' => 'filesystem',
                'query_cache' => 'filesystem',
                'result_cache' => 'array',
                'hydration_cache' => 'array',
                'generate_proxies' => false,
            ],
        ],
    ],
];
