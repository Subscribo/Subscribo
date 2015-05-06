<?php

namespace Subscribo\Omnipay\Shared;

use Omnipay\Common\Item as Base;

/**
 * Class Item extends Omnipay Common Item and adds some attributes
 *
 * Added attributes:
 *
 * * identifier (e.g. article number)
 * * taxPercent (VAT, usually as integer or integer string; in percent)
 * * discountPercent (discount, usually as integer or integer string; in percent)
 * * flags (use defined by gateway, if any)
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class Item extends Base
{
    /**
     * @return string|int|null
     */
    public function getIdentifier()
    {
        return $this->getParameter('identifier');
    }

    /**
     * @param string|int|null $value
     * @return $this
     */
    public function setIdentifier($value)
    {
        return $this->setParameter('identifier', $value);
    }

    /**
     * @return string|int|null
     */
    public function getTaxPercent()
    {
        return $this->getParameter('taxPercent');
    }

    /**
     * @param string|int|null $value
     * @return $this
     */
    public function setTaxPercent($value)
    {
        return $this->setParameter('taxPercent', $value);
    }

    /**
     * @return string|int|null
     */
    public function getDiscountPercent()
    {
        return $this->getParameter('discountPercent');
    }

    /**
     * @param string|int|null $value
     * @return $this
     */
    public function setDiscountPercent($value)
    {
        return $this->setParameter('discountPercent', $value);
    }

    /**
     * @return string|int|null|mixed
     */
    public function getFlags()
    {
        return $this->getParameter('flags');
    }

    /**
     * @param string|int|null $value
     * @return $this
     */
    public function setFlags($value)
    {
        return $this->setParameter('flags', $value);
    }
}
