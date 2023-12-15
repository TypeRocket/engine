<?php
namespace TypeRocket\Engine7\Database;

use TypeRocket\Engine7\Core\Config;
use TypeRocket\Engine7\Core\Container;
use TypeRocket\Engine7\Database\Connectors\DatabaseConnector;

class Connection
{
    const CONTAINER_ALIAS = 'typerocket.engine7.db-connection';

    /**
     * @var array<string, \wpdb>
     */
    protected array $connections = [];

    /**
     * @return array<string, \wpdb>
     */
    public function all() : array
    {
        return $this->connections;
    }

    public function add(string $name, \wpdb $wpdb) : static
    {
        if(array_key_exists($name, $this->connections)) {
            throw new \Error(__("TypeRocket database connection name \"{$name}\" already used.", 'typerocket-core'));
        }

        $this->connections[$name] = $wpdb;

        return $this;
    }

    public function addFromConfig(string $name, ?array $config = null) : static
    {
        if(is_null($config)) {
            $config = Config::getFromContainer("core.database.drivers.{$name}");
        }

        /** @var DatabaseConnector $connector */
        $connector = new $config['driver'];
        $connector->connect($name, $config);
        return $this->add($connector->getName(), $connector->getConnection());
    }

    public function getOrAddFromConfig(string $name, ?array $config = null) : \wpdb
    {
        if(!array_key_exists($name, $this->connections)) {
            $this->addFromConfig($name, $config);
        }

        return $this->connections[$name];
    }

    public function get(string $name, ?string $fallback = null) : \wpdb
    {
        if(!array_key_exists($name, $this->connections) && is_null($fallback)) {
            $this->addFromConfig($name);
        }

        return $this->connections[$name] ?? $this->connections[$fallback];
    }

    public function exists(?string $name) : bool
    {
        return !is_null($name) && array_key_exists($name, $this->connections);
    }

    public function close(string $name) : static
    {
        if(array_key_exists($name, $this->connections)) {
            $this->connections[$name]->close();
            unset($this->connections[$name]);
        }

        return $this;
    }

    public function default() : \wpdb
    {
        return $this->get(Config::getFromContainer('core.database.default', 'wp'));
    }

    public static function initDefault() : static
    {
        $name = Config::getFromContainer('core.database.default', 'wp');
        $config = Config::getFromContainer("core.database.drivers.{$name}");
        return (new static)->addFromConfig($name, $config);
    }

    public static function getFromContainer() : static
    {
        return Container::resolve(static::CONTAINER_ALIAS);
    }
}