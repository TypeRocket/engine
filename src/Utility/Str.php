<?php
namespace TypeRocket\Engine7\Utility;

use TypeRocket\Engine7\Utility\Arr;
use TypeRocket\Engine7\Utility\Data;

class Str
{
    public const UTF8 = 'UTF-8';
    public const ASCII = 'ASCII';
    public const LATIN1 = 'ISO-8859-1';

    /**
     * @param string $value
     *
     * @return false|string
     */
    public static function uppercaseWords(string $value): false|string
    {
        return mb_convert_case($value, MB_CASE_TITLE, static::UTF8);
    }

    /**
     * HTML class names helper
     *
     * @param string|array $defaults
     * @param null|string|array $classes
     * @param string $failed
     * @return string
     */
    public static function classNames(string|array $defaults, null|string|array $classes = null, string $failed = ''): string
    {
        $numArgs = func_num_args();
        $checkList = is_array($defaults) && ($numArgs === 1 || $numArgs === 2) ? $defaults : $classes;

        $result = Arr::reduceAllowedStr($checkList);
        $classes ??= '';

        if(!$result) {
            $result = is_string($classes) && $numArgs === 2 ? $classes : $failed;
        }

        $defaults = is_string($defaults) ? $defaults : '';

        return trim($defaults . ' ' . $result);
    }

    /**
     * Quiet
     *
     * Is null or is blank after trim.
     *
     * @param string|null $value
     *
     * @return bool
     */
    public static function quiet(?string $value): bool
    {
        return !isset($value) || (trim($value) === '');
    }

    /**
     * Blank
     *
     * Blank value or empty string
     *
     * @param string|null $value
     *
     * @return bool
     */
    public static function blank(?string $value): bool
    {
        return !isset($value) || $value === '';
    }

    /**
     * Not Blank
     *
     * Not blank value or empty string
     *
     * @param string|null $value
     *
     * @return bool
     */
    public static function notBlank(?string $value): bool
    {
        return !static::blank($value);
    }

    /**
     * String Ends
     *
     * @param string|null $haystack
     * @param string|null $needle
     *
     * @return bool
     */
    public static function ends(?string $haystack, ?string $needle) : bool
    {
        return str_ends_with((string)$haystack,(string) $needle);
    }

    /**
     * String Contains
     *
     * @param null|string $haystack
     * @param null|string $needle
     *
     * @return bool
     */
    public static function contains(?string $haystack, ?string $needle) : bool
    {
        return str_contains((string) $haystack,(string) $needle);
    }

    /**
     * String Starts
     *
     * @param string|null $haystack
     * @param string|null $needle
     *
     * @return bool
     */
    public static function starts(?string $haystack, ?string $needle): bool
    {
        return str_starts_with((string) $haystack, (string) $needle);
    }

    /**
     * Convert To Camel Case
     *
     * @param string|null $value
     * @param string $separator specify - or _
     * @param bool $capitalizeFirstChar define as false if you want camelCase over CamelCase
     *
     * @return mixed
     */
    public static function camelize(?string $value, string $separator = '_', bool $capitalizeFirstChar = true): mixed
    {
        $str = str_replace($separator, '', ucwords($value ?? '', $separator));

        if (!$capitalizeFirstChar) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * Snake Case
     *
     * @param string|null $value
     *
     * @return string
     */
    public static function snake(?string $value): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $value ?? '', $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * Remove Start
     *
     * @param string|null $value
     * @param string $remove
     * @return string
     */
    public static function removeStart(?string $value, string $remove = '/'): string
    {
        if (!static::starts($value, $remove)) {
            return $value ?? '';
        }

        return substr($value ?? '', strlen($remove));
    }

    /**
     * Replace First
     *
     * @param string $pattern
     * @param string $new
     * @param string $value
     * @param bool $escape
     * @return string|string[]|null
     */
    public static function replaceFirstRegex(string $pattern, string $new, string $value, bool $escape = true): array|string|null
    {
        $pattern = $escape ? '/' . preg_quote($pattern, '/') . '/' : $pattern;
        return preg_replace($pattern, $new, $value, 1);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param  string  $needle
     * @param  string  $new
     * @param  string  $value
     * @return string
     */
    public static function replaceFirst(string $needle, string $new, string $value): string
    {
        if ($needle === '') {
            return $value;
        }

        $position = strpos($value, $needle);

        if ($position !== false) {
            return substr_replace($value, $new, $position, strlen($needle));
        }

        return $value;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param  string  $needle
     * @param  string  $new
     * @param  string  $value
     * @return string
     */
    public static function replaceLast(string $needle, string $new, string $value)
    {
        if ($needle === '') {
            return $value;
        }

        $position = strrpos($value, $needle);

        if ($position !== false) {
            return substr_replace($value, $new, $position, strlen($needle));
        }

        return $value;
    }

    /**
     * Match The First Patter In List
     *
     * Used for the TypeRocket router. Returns the matched route pattern or null if no match found.
     *
     * @param array $patterns
     * @param string $value
     *
     * @return string|null
     */
    public static function pregMatchFindFirst(array $patterns, string $value): ?string
    {
        $regex = ['#^(?'];
        foreach ($patterns as $i => $pattern) {
            if($reg = is_string($pattern) ? $pattern : Data::walk(['regex'], $pattern)) {
                $regex[] = $reg . '(*MARK:'.$i.')';
            }
        }
        $regex = implode('|', $regex) . ')$#x';
        preg_match($regex, $value, $m);

        if(empty($m)) { return null; }

        $found = isset($m['MARK']) && is_numeric($m['MARK']) ? $patterns[$m['MARK']] : null;
        if(empty($found)) { return null; }

        return $found;
    }

    /**
     * Split At
     *
     * @param string $separator
     * @param string $value
     * @param bool $last
     *
     * @return array
     */
    public static function splitAt(string $separator, string $value, bool $last = false) : array
    {
        if(!$last) {
            return array_pad(explode($separator, $value, 2), 2, null);
        }

        $parts = explode($separator, $value);
        $last = array_pop($parts);
        $first = implode($separator, $parts);
        return [$first ?: null, $last];
    }

    /**
     * Explode Starting From Right
     *
     * @param string $separator
     * @param string $value
     * @param int $limit
     *
     * @return array
     */
    public static function explodeFromRight(string $separator, string $value, int $limit = PHP_INT_MAX) : array
    {
        return array_reverse(array_map('strrev', explode($separator, strrev($value), $limit)));
    }

    /**
     * Make Words
     *
     * @param string $value
     * @param string $separator
     * @param bool $uppercase
     *
     * @return false|string
     */
    public static function makeWords(string $value, string $separator = '_', bool $uppercase = false) : false|string
    {
        $words = str_replace($separator, ' ', $value);
        return $uppercase ? static::uppercaseWords($words) : $words;
    }

    /**
     * Limit Length
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     *
     * @return string
     */
    public static function limit(string $value, int $limit, string $end = '') : string
    {
        $length = static::length($value, static::UTF8);

        if ($length <= $limit) {
            return $value;
        }

        $width = mb_strwidth($value, static::UTF8) - $length;

        return rtrim(mb_strimwidth($value, 0, $limit + $width, '', static::UTF8)).$end;
    }

    /**
     * Length
     *
     * @param string $value
     * @param string|null $encoding
     *
     * @return int
     */
    public static function length(string $value, ?string $encoding = null) : int
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    /**
     * Maxed
     *
     * Is string max length
     *
     * @param string $value
     * @param int $max
     * @param string|null $encoding
     *
     * @return bool
     */
    public static function maxed(string $value, int $max, ?string $encoding = null) : bool
    {
        return !(static::length($value, $encoding) <= $max);
    }

    /**
     * Min
     *
     * Is string min length
     *
     * @param string $value
     * @param int $min
     * @param string|null $encoding
     *
     * @return bool
     */
    public static function min(string $value, int $min, ?string $encoding = null) : bool
    {
        return static::length($value, $encoding) >= $min;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     * @param string|null $encoding
     *
     * @return string
     */
    public static function lower(string $value, ?string $encoding = null) : string
    {
        return mb_strtolower($value, $encoding ?? static::UTF8);
    }

    /**
     * Get Encoding
     *
     * @param string|null $encoding
     *
     * @return string
     */
    public static function encoding(?string $encoding = null) : string
    {
        $encoding = $encoding ?? mb_internal_encoding();
        return ! static::quiet($encoding) ? $encoding : static::UTF8;
    }

    /**
     * Reverse
     *
     * @param string $value
     *
     * @return string
     */
    public static function reverse(string $value) : string
    {
        return implode(array_reverse(mb_str_split($value)));
    }

}