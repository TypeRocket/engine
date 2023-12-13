<?php
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

TypeRocketEngine7Autoloader::autoloadPsr4($typerocket_autoload_map);

do_action('typerocket_engine7_loaded');