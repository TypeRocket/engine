<?php
namespace TypeRocket\Engine7\Interfaces;

interface ResolvesWith
{
    public function onResolution(): static;
}