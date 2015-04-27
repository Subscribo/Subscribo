<?php namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\AbstractRequest as Base;
use Omnipay\Common\CreditCard;
use Subscribo\Omnipay\Shared\CreditCard as ExtendedCreditCard;
use Subscribo\Omnipay\Shared\Traits\HttpMessageSendingTrait;


/**
 * Abstract class AbstractRequest
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractRequest extends Base
{
    use HttpMessageSendingTrait;

    public function setCard($value)
    {
        if ($value) {
            if (($value instanceof CreditCard) and ! ($value instanceof ExtendedCreditCard)) {
                $value = $value->getParameters();
            }
            if ( ! ($value instanceof ExtendedCreditCard)) {
                $value = new ExtendedCreditCard($value);
            }
        }
        return $this->setParameter('card', $value);
    }
}
