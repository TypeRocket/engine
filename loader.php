<?php
/**
 * Register TypeRocket Engine7
 *
 * 1. Install TypeRocket Engine7 with composer but do not include the composer autoload.php file.
 * 2. Include this file in your plugin.
 * 3. Add your TypeRocket code to the action hook typerocket_engine7_loaded
 *
 * include __DIR__ . '/vendor/typerocket/engine/loader.php';
 *
 * add_action('typerocket_engine7_loaded', function() {
 *   // Your code here
 * });
 */
if(!function_exists('typerocket_engine7_register')) {
    function typerocket_engine7_register(?string $version = null, ?string $location = null) {
        static $v = '0:0';

        [$number, $directory] = explode(':', $v);

        if($version === null) {
            return explode(':', $v);
        }

        $v = max($number, $version);

        if($v === $version) {
            $v = $v.':'.$location;
        } else {
            $v = $v.':'.$directory;
        }

        return $v;
    }

    add_action('after_setup_theme', function() {
        [$number, $directory] = typerocket_engine7_register();
        require_once $directory . '/vendor/autoload.php';
        require_once $directory . '/init.php';
    });
}

typerocket_engine7_register('7.0.0', __DIR__);