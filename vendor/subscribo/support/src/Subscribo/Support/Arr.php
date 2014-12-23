<?php namespace Subscribo\Support;

/**
 * Class Arr
 * Simply extending Laravel Framework class
 *
 * @package Subscribo\Support
 */
class Arr extends \Illuminate\Support\Arr {


    public static function merge_natural(array $array1, array $array2)
    {
        $result = $array1;
        foreach ($array2 as $key => $value) {
            if (isset($result[$key]) and is_array($result[$key]) and is_array($value)) {
                $result[$key] = self::merge_natural($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
