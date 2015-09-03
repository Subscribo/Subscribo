<?php

namespace Subscribo\Support;

use DateTime;
use DateTimeZone;

/**
 * Class DateTimeUtils
 * @package Subscribo\Support
 */
class DateTimeUtils
{
    /**
     * @param DateTime|string|null $time
     * @param DateTimeZone|null $timezone
     * @return DateTime|null
     */
    public static function makeDateTime($time = 'now', DateTimeZone $timezone = null)
    {
        if (empty($time)) {

            return null;
        }
        if ($time instanceof DateTime) {
            if ($timezone) {
                $time->setTimezone($timezone);
            }

            return $time;
        }

        return new DateTime($time, $timezone);
    }

    /**
     * @param DateTime|string|null $time
     * @param bool|null $startOfDay true for start of day, false for end of day, null for no time change
     * @param DateTimeZone $timezone
     * @return DateTime|null
     */
    public static function makeDate($time, $startOfDay = true, DateTimeZone $timezone = null)
    {
        $result = static::makeDateTime($time, $timezone);
        if (empty($result)) {

            return null;
        }
        if (true === $startOfDay) {
            $result->setTime(0, 0, 0);
        } elseif (false === $startOfDay) {
            $result->setTime(23, 59, 59);
        }
        //Do nothing if $startOfDay is null

        return $result;
    }

    /**
     * @param string|DateTime $time
     * @param string $format
     * @return bool|float|int|null|string
     */
    public static function exportDateTime($time, $format = 'Y-m-d H:i:s')
    {
        if ($time instanceof DateTime) {

            return $time->format($format);
        }
        if (is_scalar($time)) {

            return $time;
        }

        return null;
    }
}
