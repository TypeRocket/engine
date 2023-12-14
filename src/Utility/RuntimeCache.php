<?php
namespace TypeRocket\Engine7\Utility;

use TypeRocket\Engine7\Core\Container;
use TypeRocket\Engine7\Utility\Data;

class RuntimeCache
{
    public const CONTAINER_ALIAS = 'typerocket.engine7.runtime-cache';

    public function __construct(
        protected array $cache = [],
        protected array $readonly = [],
    ) {}

    public function addReadonly(string $key, mixed $data): static
    {
        $this->add($key, $data);
        $this->readonly[$key] = true;

        return $this;
    }

    public function throwExceptionIfReadonly(string $key): void
    {
        if(!empty($this->readonly[$key])) {
            throw new \Exception("Runtime cache is readonly. key:{$key}");
        }
    }

    public function add(string $key, mixed $data): static
    {
        if( $this->exists($key) ) {
            throw new \Exception("Runtime cache already set. key:{$key}");
        }

        $this->cache[$key] = Data::value($data);

        return $this;
    }

    public function update(string $key, mixed $data): static
    {
        $this->throwExceptionIfReadonly($key);

        $this->cache[$key] = $data;

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache[$key] ?? $default;
    }

    public function walk(string $key, string $dots, mixed $default = null): mixed
    {
        return Data::walk($dots, $this->cache[$key], $default);
    }

    public function getOtherwisePut(string $key, mixed $default = null): mixed
    {
        if($value = $this->get($key)) {
            return $value;
        }

        return $this->add($key, $default);
    }

    public function delete(string $key): static
    {
        $this->throwExceptionIfReadonly($key);

        $this->cache[$key] = null;

        return $this;
    }

    public function exists(string $key): bool
    {
        return !empty($this->cache[$key]);
    }

    public static function getFromContainer(): static
    {
        return Container::resolve(static::CONTAINER_ALIAS);
    }

    public static function new(...$args): static
    {
        return new static(...$args);
    }
}