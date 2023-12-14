<?php
namespace TypeRocket\Engine7\Models;

/**
 * Class is only a stub for the container
 *
 * Container loads App\User
 */
interface AuthUser
{
    public const CONTAINER_ALIAS = 'typerocket.engine7.auth-user';

    public function isCurrent() : bool;

    public function isCapable(string $capability) : bool;

    public function getID() : mixed;
}