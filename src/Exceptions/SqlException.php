<?php
namespace TypeRocket\Engine7\Exceptions;

class SqlException extends \Exception
{
    protected string $sql;
    protected string $sqlError;

    public function setSql(string $sql) : static
    {
        $this->sql = $sql;

        return $this;
    }

    public function getSql() : ?string
    {
        return $this->sql;
    }

    public function setSqlError(string $sql) : static
    {
        $this->sqlError = $sql;

        return $this;
    }

    public function getSqlError() : ?string
    {
        return $this->sqlError;
    }
}