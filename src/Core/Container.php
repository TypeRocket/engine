<?php
namespace TypeRocket\Engine7\Core;

class Container
{
    protected static array $list = [];
    protected static array $alias = [];

    /**
     * Resolve Class
     *
     * @param string $class class name or alias
     * @param bool $forceAliasLookup only check for instance by alias
     *
     * @return mixed|null
     */
    public static function resolve(string $class, bool $forceAliasLookup = false): mixed
    {
        if(!$forceAliasLookup && array_key_exists($class, self::$list)) {
            $single = self::$list[$class]['singleton_instance'];

            if($single) {
                return $single;
            }

            $instance = call_user_func(self::$list[$class]['callback']);

            if(!empty(self::$list[$class]['make_singleton'])) {
                self::$list[$class]['singleton_instance'] = $instance;
            }

            return $instance;
        }

        return self::resolveAlias($class);
    }

    /**
     * Resolve by Alias Only
     *
     * @param string $alias alias
     *
     * @return mixed|null
     */
    public static function resolveAlias(string $alias): mixed
    {
        if(!empty(self::$alias[$alias])) {
            return self::resolve(self::$alias[$alias]);
        }

        return null;
    }

    /**
     * Register Class
     *
     * @param string $class
     * @param callable $callback
     * @param bool $singleton
     * @param null|string $alias
     * @return bool
     */
    public static function register(string $class, callable $callback, bool $singleton = false, ?string $alias = null) : bool
    {
        if(!empty(self::$list[$class])) {
            return false;
        }

        self::$list[$class] = [
            'callback' => $callback,
            'make_singleton' => $singleton,
            'singleton_instance' => null
        ];

        if($alias && empty(self::$alias[$alias])) {
            self::$alias[$alias] = $class;
        }

        return true;
    }

    /**
     * Register Singleton
     *
     * @param string $class
     * @param callable $callback
     * @param null|string  $alias
     *
     * @return bool
     */
    public static function singleton(string $class, callable $callback, ?string $alias = null) : bool
    {
        return self::register($class, $callback, true, $alias);
    }

    /**
     * Get Aliases
     *
     * @return array
     */
    public static function aliases() : array
    {
        return self::$alias;
    }

    /**
     * Alias Exists
     *
     * @param string $alias
     * @return bool
     */
    public static function aliasExists(string $alias) : bool
    {
        return !empty(self::$alias[$alias]);
    }

    /**
     * Find Or New Singleton
     *
     * Does not work on classes with a constructor
     *
     * @param string $class
     * @param null|string $alias
     *
     * @return mixed
     */
    public static function findOrNewSingleton(string $class, ?string $alias = null) : mixed
    {
        self::register($class, function() use ($class) {
            return new $class;
        }, true, $alias);

        return self::resolve($class);
    }

}