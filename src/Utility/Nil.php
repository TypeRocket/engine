<?php
namespace TypeRocket\Engine7\Utility;

use ArrayAccess;

class Nil implements ArrayAccess
{
    protected mixed $value;

    /**
     * @param mixed $value
     */
    public function __construct(mixed $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return mixed|null
     */
    public function get() : mixed
    {
        return $this->value instanceof Nil ? $this->value->get() : $this->value;
    }

    /**
     * @param string|int $key
     *
     * @return Nil
     */
    public function __get(string|int $key) : Nil
    {
        return new Nil($this->value->{$key} ?? new Nil);
    }

    /**
     * @param string|int $name
     *
     * @return bool
     */
    public function __isset(string|int $name) : bool
    {
        if (is_object($this->value)) {
            return isset($this->value->{$name});
        }

        if ($this->arrayCheck()) {
            return isset($this->value[$name]);
        }

        return false;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset) : bool
    {
        return $this->arrayCheck($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return Nil
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset) : Nil
    {
        return new Nil($this->value[$offset] ?? new Nil);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value)  : void
    {
        if ($this->arrayCheck()) {
            $this->value[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset) : void
    {
        if ($this->arrayCheck()) {
            unset($this->value[$offset]);
        }
    }

    /**
     * @param string|null $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    protected function arrayCheck(?string $offset = null) : bool
    {
        if(is_array($this->value) || $this->value instanceof ArrayAccess) {

            if($offset && !($this->value[$offset] ?? null)) {
                return false;
            }

            return true;
        }

        return false;
    }
}