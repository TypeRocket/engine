<?php
namespace TypeRocket\Engine7\Utility\Traits;

trait ArrayAccessible
{
    /**
     * @var array
     */
    protected array $_items = [];

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value) : void
    {
        $location = $this->_location ?? '_items';

        if (is_null($offset)) {
            $this->{$location}[] = $value;
        } else {
            $this->{$location}[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset) : bool
    {
        $location = $this->_location ?? '_items';

        return isset($this->{$location}[$offset]);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset) : void
    {
        $location = $this->_location ?? '_items';

        unset($this->{$location}[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        $location = $this->_location ?? '_items';

        return $this->{$location}[$offset] ?? null;
    }
}