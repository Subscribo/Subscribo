<?php

namespace Subscribo\Support;

use InvalidArgumentException;

/**
 * Class Math
 *
 * @package Subscribo\Support
 */
class Math
{
    const ROUND_HALF_AWAY_FROM_ZERO = 'ROUND_HALF_AWAY_FROM_ZERO';

    /**
     * @param string $value
     * @param int $scale
     * @param string $mode
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function bcround($value, $scale, $mode = self::ROUND_HALF_AWAY_FROM_ZERO)
    {
        if ($mode !== self::ROUND_HALF_AWAY_FROM_ZERO) {
            throw new InvalidArgumentException('Wrong mode. Currently only ROUND_HALF_AWAY_FROM_ZERO is supported.');
        }
        if ($scale < 0) {
            throw new InvalidArgumentException('Negative scale is not supported.');
        }
        $half = bcdiv('5', bcpow('10', $scale + 1), $scale + 1);

        if (bccomp($value, '0', strlen($value)) === -1) {
            ///Negative number
            return bcsub($value, $half, $scale);
        }

        return bcadd($value, $half, $scale);
    }
}
