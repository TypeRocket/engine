<?php
use TypeRocket\Engine7\Core\Config;
use \TypeRocket\Engine7\Database\Connectors\WordPressCoreDatabaseConnector;

return \TypeRocket\Engine7\Core\Hook::applyFilters('core_config', [
    'debug' => Config::env('WP_DEBUG', true),

    'database' => [
        'default' => Config::env('TYPEROCKET_DATABASE_DEFAULT', 'wp'),
        'drivers' => [
            'wp' => [
                'driver' => WordPressCoreDatabaseConnector::class,
            ],
        ]
    ]
]);