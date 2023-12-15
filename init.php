<?php
/**
 * @var string $typerocket_number latest version installed
 * @var string $typerocket_directory latest version directory
 * @var array $typerocket_autoload_map
 */
if(defined('TYPEROCKET_ENGINE7')) {
   return;
}

define('TYPEROCKET_ENGINE7', $typerocket_number);

class TypeRocketEngine7Autoloader {
    public static function autoloadPsr4(array &$map = [], $prepend = false)
    {
        if (isset($map['init'])) {
            foreach ($map['init'] as $file) {
                require $file;
            }
        }
        spl_autoload_register(function ($class) use (&$map) {
            if (isset($map['map'][$class])) {
                require $map['map'][$class];
                return;
            }
            $prefix = $map['prefix'];
            $folder = $map['folder'];
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $file = $folder . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $len)) . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }, true, $prepend);
    }
}

const TYPEROCKET_ENGINE7_PATH = __DIR__;
const TYPEROCKET_ENGINE7_CONFIG_PATH = TYPEROCKET_ENGINE7_PATH . '/config';
TypeRocketEngine7Autoloader::autoloadPsr4($typerocket_autoload_map);
\TypeRocket\Engine7\Core\Booter::init();

do_action('typerocket_engine7_loaded');