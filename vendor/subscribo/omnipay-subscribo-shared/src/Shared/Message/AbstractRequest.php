<?php namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\AbstractRequest as Base;
use Subscribo\Omnipay\Shared\Traits\HttpMessageSendingTrait;
use Subscribo\Omnipay\Shared\Traits\ParameterHandlingTrait;

/**
 * Abstract class AbstractRequest
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractRequest extends Base
{
    use HttpMessageSendingTrait;
    use ParameterHandlingTrait;

}
