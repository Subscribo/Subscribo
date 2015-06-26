<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericPostRequest;

/**
 * Class PostPurchaseRequest
 *
 * @package Omnipay\PayUnity
 */
class PostPurchaseRequest extends GenericPostRequest
{
    protected $defaultPaymentType = 'DB';

    public function getData()
    {
        $this->validate('amount', 'currency');

        return parent::getData();
    }
}
