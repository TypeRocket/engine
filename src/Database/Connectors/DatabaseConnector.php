<?php
namespace TypeRocket\Engine7\Database\Connectors;

abstract class DatabaseConnector
{
    protected string $name;
    protected \wpdb $wpdb;

    /**
     * @return bool
     */
    public function isConnected() : bool
    {
        return isset($this->name, $this->wpdb);
    }

    /**
     * @param string|null $name
     * @param array $args
     */
    abstract public function connect(?string $name = null, array $args = []) : static;

    public function getName() : string
    {
        return $this->name;
    }

    public function getConnection(): \wpdb
    {
        return $this->wpdb;
    }
}