<?php
namespace TypeRocket\Engine7\Database;

class SqlRaw
{
    protected string $sql = '';

    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }

    public function __toString()
    {
        return $this->sql;
    }

    public static function new(...$args) : static
    {
        return new static(...$args);
    }
}