<?php
namespace TypeRocket\Engine7\Elements\Traits;

trait Attributes
{
    protected array $attr = [];

    /**
     * Attribute Shorthand
     *
     * @param null|string|array $key
     * @param mixed $value
     */
    public function attr(null|string|array $key = null, mixed $value = null) : mixed
    {
        $num = func_num_args();

        if($num == 0) {
            return $this->getAttributes();
        }

        if(is_array($key)) {
            return $this->attrExtend($key);
        }

        if($num == 1) {
            return $this->getAttribute($key);
        }

        return $this->setAttribute($key, $value);
    }

    /**
     * Set Attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function attrReset( array $attributes = [] ) : static
    {
        $this->attr = $attributes;

        return $this;
    }

    /**
     * Extend Attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function attrExtend( array $attributes ) : static
    {
        $this->attr = array_merge($this->attr, $attributes);

        return $this;
    }

    /**
     * Get Attribute by key
     *
     * @param null|array $with
     *
     * @return array
     */
    public function getAttributes(?array $with = null) : array
    {
        return !$with ? $this->attr : array_merge($this->attr, $with);
    }

    /**
     * Set Attribute by key
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute(string $key, mixed $value = '' ) : static
    {
        if(!is_null($value)) {
            $this->attr[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param null|mixed $default
     */
    public function getAttribute(string $key, mixed $default = null ) : mixed
    {
        if ( ! array_key_exists( $key, $this->attr )) {
            return $default;
        }

        return $this->attr[$key];
    }

    /**
     * Get all options and then set the list to
     * an empty array.
     *
     * @return array
     */
    public function popAllAttributes() : array
    {
        $options = $this->attr;
        $this->attr = [];

        return $options;
    }

    /**
     * @return mixed
     */
    public function popAttribute() : mixed
    {
        return array_pop($this->attr);
    }

    /**
     * @return mixed
     */
    public function shiftAttribute() : mixed
    {
        return array_shift($this->attr);
    }

    /**
     * Remove Attribute by key
     *
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute( string $key ) : static
    {

        if (array_key_exists( $key, $this->attr )) {
            unset( $this->attr[$key] );
        }

        return $this;
    }

    /**
     * Append a string to an attribute
     *
     * @param null|string $value the string to append
     *
     * @return $this|string|null
     */
    public function attrClass(?string $value = null) : static|null|string
    {
        if(func_num_args() === 0) {
            return $this->attr['class'] ?? null;
        }

        $this->attr['class'] = trim(($this->attr['class'] ?? '' ) . ' ' . $value);

        return $this;
    }

    /**
     * @param bool $bool
     * @param string $value
     */
    public function attrClassIf(bool $bool, string $value) : static
    {
        if($bool) {
            $this->attrClass($value);
        }

        return $this;
    }

    /**
     * Maybe Set Attribute
     *
     * @param string $key
     * @param mixed|null $value value to set if none exists
     */
    public function maybeSetAttribute( string $key, mixed $value = null ) : static
    {
        if ( ! $this->getAttribute($key) ) {
            $this->attr[$key] = $value;
        }

        return $this;
    }

}