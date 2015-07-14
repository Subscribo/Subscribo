<?php namespace Subscribo\Support;

/**
 * Class Arr
 * Extending Laravel Framework class and providing some additional functionality
 *
 * @package Subscribo\Support
 */
class Arr extends \Illuminate\Support\Arr {


    /**
     * Merges two arrays recursive "naturally"
     * Second array values takes precedence, with exception that its value on particular place is null
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeNatural(array $array1, array $array2)
    {
        if (empty($array1)) {
            return $array2;
        }
        $result = $array1;
        foreach ($array2 as $key => $value) {
            if (isset($result[$key]) and is_array($result[$key]) and is_array($value)) {
                $result[$key] = static::mergeNatural($result[$key], $value);
            } elseif ( ( ! isset($result[$key]) or ( ! is_null($value)))) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Filter haystack using values in needles in case insensitive manner if needed
     *
     * Exact match takes precedence,
     * If exact match is not found, then case insensitive comparison is made
     *
     * @param array $needles
     * @param array $haystack
     * @return array
     */
    public static function filterCaseInsensitively(array $needles, array $haystack)
    {
        $result = array();
        $modifiedHaystack = array_change_key_case($haystack, CASE_LOWER);
        foreach ($needles as $needle) {
            $modifiedNeedle = strtolower($needle);
            if (array_key_exists($needle, $haystack)) {
                $result[$needle] = $haystack[$needle];
            } elseif (array_key_exists($modifiedNeedle, $modifiedHaystack)) {
                $result[$needle] = $modifiedHaystack[$modifiedNeedle];
            }
        }
        return $result;
    }

    public static function getSimpleCaseInsensitively($array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        $modifiedArray = array_change_key_case($array, CASE_LOWER);
        $modifiedKey = strtolower($key);
        if (array_key_exists($modifiedKey, $modifiedArray)) {
            return $modifiedArray[$modifiedKey];
        }
        return $default;
    }

    /**
     * @param string $needle
     * @param array $haystack
     * @return array
     */
    public static function withoutKeyCaseInsensitively($needle, array $haystack)
    {
        $result = array();
        $modifiedNeedle = strtolower($needle);
        foreach ($haystack as $key => $value) {
            $modifiedKey = strtolower($key);
            if ($modifiedKey != $modifiedNeedle) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
