<?php namespace Subscribo\Support;

use Subscribo\Support\Arr;

/**
 * Class Str
 * Simply extending Laravel framework class
 *
 * @package Subscribo\Support
 */
class Str extends \Illuminate\Support\Str
{
    /**
     * Parses URL query into (possible multidimensional) array
     * @param string $query
     * @return array
     */
    public static function parseUrlQuery($query)
    {
        $result = [];
        $pairs = explode('&', $query);
        foreach ($pairs as $pair) {
            $parts = explode('=', $pair);
            $rawKey = $parts[0];
            $rawValue = isset($parts[1]) ? $parts[1] : '';
            $value = urldecode($rawValue);
            $key = strtr(urldecode($rawKey), ['[' => '.', ']' => '']);
            Arr::set($result, $key, $value);
        }
        return $result;
    }

    public static function buildUrl($parts)
    {
        if ( ! empty($parts['host'])) {
            $result = $parts['host'];
            if ( ! empty($parts['scheme'])) {
                $result = $parts['scheme'].'://'.$result;
            }
            if ( ! empty($parts['port'])) {
                $result .= ':'.$parts['port'];
            }
        } else {
            $result = '';
        }
        if ( ! empty($parts['path'])) {
            $result .= $parts['path'];
        }
        if ( ! empty($parts['query'])) {
            $result .= '?'.$parts['query'];
        }
        return $result;
    }
}
