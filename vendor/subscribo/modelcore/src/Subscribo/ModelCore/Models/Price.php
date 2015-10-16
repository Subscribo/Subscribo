<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\Support\Math;
use Subscribo\ModelCore\Models\Currency;


/**
 * Model Price
 *
 * Model class for being changed and used in the application
 */
class Price extends \Subscribo\ModelCore\Bases\Price
{
    /**
     * @param int $countryId
     * @return bool
     */
    public function isForCountryId($countryId)
    {
        foreach ($this->countries as $country) {
            if ($country->id === $countryId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isGratis()
    {
        return Currency::amountIsEmpty($this->amount);
    }

    /**
     * @param int|string $taxPercent
     * @return string
     */
    public function calculateNetAmount($taxPercent)
    {
        if ($this->isGratis()) {
            return '0';
        }
        $stringAmount = strval($this->amount);
        if ($this->priceType === 'net') {

            return Math::bcround($stringAmount, $this->currency->precision);
        }
        $multiplier = $this->calculateTaxMultiplier($taxPercent);
        $netAmount = bcdiv($stringAmount, $multiplier, $this->currency->precision + 2);
        $rounded = Math::bcround($netAmount, $this->currency->precision);

        return $rounded;
    }

    /**
     * @param int|string $taxPercent
     * @return string
     */
    public function calculateGrossAmount($taxPercent)
    {
        if ($this->isGratis()) {
            return '0';
        }
        $netAmount = $this->calculateNetAmount($taxPercent);
        $multiplier = $this->calculateTaxMultiplier($taxPercent);
        $grossAmount = bcmul($netAmount, $multiplier, $this->currency->precision + 2);
        $rounded = Math::bcround($grossAmount, $this->currency->precision);

        return $rounded;
    }

    /**
     * @param int|string $taxPercent
     * @return string
     */
    protected function calculateTaxMultiplier($taxPercent)
    {
        if (empty($taxPercent)) {
            return '0';
        }
        $taxPercentString = strval($taxPercent);
        $precision = strlen($taxPercentString) + 2;
        $realNumber = bcdiv($taxPercentString, '100', $precision);
        $multiplier = bcadd('1', $realNumber, $precision);

        return $multiplier;
    }
}
