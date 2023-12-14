<?php
/**
 * Register TypeRocket Engine7
 *
 * Install with composer using its autoload.php file:
 *
 * include __DIR__ . '/vendor/autoload.php';
 *
 * Or, the below steps:
 *
 * 1. Download TypeRocket Engine7 into your project.
 * 2. Include this file in your plugin.
 *
 * include __DIR__ . '/{your_downloaded_folder_name}/loader.php';
 */
if(!function_exists('typerocket_engine7_register')) {
    function typerocket_engine7_register(?string $version = null, ?string $location = null) {
        static $v = '0:0';

        [$number, $directory] = explode(':', $v);

        if($version === null) {
            return explode(':', $v);
        }

        $v = version_compare($version, $number, '>') ? $version : $number;

        if($v === $version) {
            $v = $v.':'.$location;
        } else {
            $v = $v.':'.$directory;
        }

        return $v;
    }

    add_action('after_setup_theme', function() {
        [$typerocket_number, $typerocket_directory] = typerocket_engine7_register();
        $typerocket_autoload_map = [
            'prefix' => 'TypeRocket\\Engine7\\',
            'folder' => $typerocket_directory . '\vendor\typerocket\engine'
        ];

        require_once $typerocket_directory . '/init.php';
    });
}

typerocket_engine7_register('7.0.2', __DIR__);
