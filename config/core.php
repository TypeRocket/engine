<?php
use TypeRocket\Engine7\Core\Config;
use \TypeRocket\Engine7\Database\Connectors\WordPressCoreDatabaseConnector;

return [
    'debug' => Config::env('WP_DEBUG', true),

    'database' => [
        'driver' => WordPressCoreDatabaseConnector::class,
    ]
];