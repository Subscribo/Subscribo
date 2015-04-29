<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;
use Omnipay\PayUnity\Traits\CopyAndPayWidgetResponseTrait;

/**
 * Class CopyAndPayPurchaseResponse
 *
 * @package Omnipay\PayUnity
 */
class CopyAndPayPurchaseResponse extends AbstractResponse
{
    use CopyAndPayWidgetResponseTrait;
}
