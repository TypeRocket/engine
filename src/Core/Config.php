<?php
namespace TypeRocket\Engine7\Core;

use TypeRocket\Engine7\Core\Container;
use TypeRocket\Engine7\Utility\Data;

class Config
{
    public const CONTAINER_ALIAS = 'typerocket.engine7.config';

    protected ?string $directory;
    protected array $config = [];

    /**
     * Set initial values
     *
     * @param array|string $data
     */
    public function __construct( array|string $data )
    {
        $this->directory = is_string($data) ? $data : null;
        $this->config = $this->directory ? [] : $data;
    }

    /**
     * Just In Time Config Loader
     *
     * @param string $dots
     * @param mixed $default
     */
    private function jitLocate(string $dots, mixed $default = null) : mixed
    {
        [$root, $rest] = array_pad(explode('.', $dots, 2), 2, null);
        if(!isset($this->config[$root]) && is_file($this->directory . '/' . $root . '.php')) {
            $this->config[$root] = require( $this->directory . '/' . $root . '.php' );
        }

        return $rest ? $this->config[$root] : Data::walk($rest, $this->config[$root], $default);
    }

    /**
     * Locate Config Setting
     *
     * Traverse array with dot notation.
     *
     * @param string $dots dot notation key.next.final
     * @param null|mixed $default default value to return if null
     */
    public function get(string $dots, mixed $default = null) : mixed
    {
        $value = Data::walk($dots, $this->config);

        return $value ?? self::jitLocate($dots, $default);
    }

    /**
     * Get Constant Variable
     *
     * @param string $name the constant variable name
     * @param null|mixed $default The default value
     * @param bool $env Try getting env data
     */
    public static function env(string $name, mixed $default = null, bool $env = false) : mixed
    {
        if($env && (!empty($_SERVER) || !empty($_ENV))) {
            if($env = $_ENV[$name] ?? $_SERVER[$name] ?? null) {
                return $env;
            }
        }

        return defined($name) ? constant($name) : $default;
    }

    /**
     * Locate Config Setting
     *
     * Traverse array with dot notation.
     *
     * @param string $dots dot notation key.next.final
     * @param null|mixed $default default value to return if null
     */
    public static function getFromContainer(string $dots, mixed $default = null) : mixed
    {
        return Container::resolve(static::CONTAINER_ALIAS)->get($dots, $default);
    }
}
