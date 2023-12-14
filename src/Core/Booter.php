<?php

namespace TypeRocket\Engine7\Core;

use TypeRocket\Engine7\Utility\RuntimeCache;

class Booter
{
    public static function init() : void
    {
        Container::singleton(
            RuntimeCache::class,
            fn() => new RuntimeCache(['typerocket.booted' => true], ['typerocket.booted' => true]),
            RuntimeCache::CONTAINER_ALIAS
        );
    }
}