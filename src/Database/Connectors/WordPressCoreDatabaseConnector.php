<?php
namespace TypeRocket\Engine7\Database\Connectors;

class WordPressCoreDatabaseConnector extends DatabaseConnector
{
    public function connect(?string $name = null, array $args = []) : static
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $this->name = 'wp';
        $this->wpdb = $wpdb;

        return $this;
    }
}