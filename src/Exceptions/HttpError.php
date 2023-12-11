<?php
namespace TypeRocket\Engine7\Exceptions;

class HttpError extends \Requests_Exception_HTTP
{
    /**
     * Get WP Error
     *
     * @param int $code
     * @param null|string $message
     */
    public static function abort(int $code = 404, ?string $message = null)
    {
        $class = static::get_class($code);
        throw new $class($message);
    }
}