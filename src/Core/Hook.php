<?php

namespace TypeRocket\Engine7\Core;

class Hook
{
    const NAMESPACE = 'typerocket_engine7_';
    public static function applyFilters($name, $value, ...$args) : mixed
    {
        return apply_filters(static::NAMESPACE . $name, $value, ...$args);
    }

    public static function doAction($name, ...$args) : void
    {
        do_action(static::NAMESPACE . $name, ...$args);
    }

    public static function addAction($name, $callback, $priority = 10, $accepted_args = 1) : true
    {
        return add_action(static::NAMESPACE . $name, $callback, $priority, $accepted_args);
    }

    public static function addFilter($name, $callback, $priority = 10, $accepted_args = 1) : true
    {
        return add_filter(static::NAMESPACE . $name, $callback, $priority, $accepted_args);
    }
}