<?php namespace Subscribo\Support;

/**
 * Class Str
 * Simply extending Laravel framework class
 *
 * @package Subscribo\Support
 */
class Str extends \Illuminate\Support\Str
{
    public static function natural($value)
    {
        $withSpaces = self::snake($value, ' ');
        $result = ucfirst($withSpaces);
        return $result;
    }
}
