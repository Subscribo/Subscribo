<?php

namespace Omnipay\PayUnity;

use Omnipay\PayUnity\AbstractGateway;

class COPYandPAYGateway extends AbstractGateway
{


    public function purchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseRequest', $parameters);
    }

}
