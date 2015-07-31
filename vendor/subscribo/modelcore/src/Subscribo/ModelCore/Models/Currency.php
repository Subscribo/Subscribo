<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelBase\Traits\SearchableByIdentifierTrait;
use InvalidArgumentException;

/**
 * Model Currency
 *
 * Model class for being changed and used in the application
 */
class Currency extends \Subscribo\ModelCore\Bases\Currency
{
    use SearchableByIdentifierTrait;

    /**
     * @param string|int $amount
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function amountIsEmpty($amount)
    {
        if (empty($amount)) {

            return true;
        }
        if ( ! is_numeric($amount)) {
            throw new InvalidArgumentException('Provided amount is not numeric');
        }
        if (is_int($amount)) {

            return false;
        }
        if (is_float($amount)) {

            return false;
        }
        $pattern1 = '/^-?0+.?0*$/';
        $pattern2 = '/^-?.0+$/';
        $trimmed = trim($amount);

        return preg_match($pattern1, $trimmed) or preg_match($pattern2, $trimmed);
    }

    /**
     * @param string|int $amount
     * @param bool $allowNegative
     * @param bool $allowEmpty
     * @return bool
     */
    public function checkAmount($amount, $allowNegative = false, $allowEmpty = false)
    {
        if (empty($amount)) {

            return $allowEmpty;
        }
        if ( ! is_numeric($amount)) {

            return false;
        }
        if (static::amountIsEmpty($amount)) {

            return $allowEmpty;
        }
        if (is_int($amount)) {

            return ($allowNegative or ($amount > 0));
        }
        if (is_float($amount)) {

            return false;
        }

        for ($i = 0; $i <= $this->precision; $i++) {
            if ($amount === bcadd('0', $amount, $i)) {

                return ($allowNegative or (false === strpos($amount, '-')));
            }
        }

        return false;
    }

    /**
     * @param string|int $amount
     * @return string
     */
    public function normalizeAmount($amount)
    {
        return bcadd('0', trim($amount), $this->precision);
    }
}
