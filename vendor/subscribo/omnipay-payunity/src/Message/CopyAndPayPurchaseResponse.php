<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;
use Omnipay\PayUnity\Traits\CopyAndPayWidgetResponseTrait;

class CopyAndPayPurchaseResponse extends AbstractResponse
{
    use CopyAndPayWidgetResponseTrait;
}
