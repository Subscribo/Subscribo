<?php

namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\AbstractRequest as Base;
use Omnipay\Common\CreditCard;
use Omnipay\Common\ItemBag;
use Subscribo\Omnipay\Shared\CreditCard as ExtendedCreditCard;
use Subscribo\Omnipay\Shared\ItemBag as ExtendedItemBag;


/**
 * Abstract class AbstractRequest
 *
 * @package Subscribo\OmnipaySubscriboShared
 *
 * @method \Subscribo\Omnipay\Shared\CreditCard|null getCard() getCard()
 * @method \Subscribo\Omnipay\Shared\ItemBag|null getItems() getItems()
 */
abstract class AbstractRequest extends Base
{
    /**
     * @param CreditCard|\Subscribo\Omnipay\Shared\CreditCard|array $value
     * @return $this
     */
    public function setCard($value)
    {
        if ($value) {
            if (($value instanceof CreditCard) and ! ($value instanceof ExtendedCreditCard)) {
                $value = $value->getParameters();
            }
            if (( ! ($value instanceof ExtendedCreditCard))) {
                $value = new ExtendedCreditCard($value);
            }
        } else {
            $value = null;
        }
        return $this->setParameter('card', $value);
    }

    /**
     * @param ItemBag|\Subscribo\Omnipay\Shared\ItemBag|array $items
     * @return $this
     */
    public function setItems($items)
    {
        if ($items) {
            if (($items instanceof ItemBag) and ! ($items instanceof ExtendedItemBag)) {
                $items = $items->all();
            }
            if (( ! ($items instanceof ExtendedItemBag))) {
                $items = new ExtendedItemBag($items);
            }
        } else {
            $items = null;
        }
        return $this->setParameter('items', $items);
    }
}
