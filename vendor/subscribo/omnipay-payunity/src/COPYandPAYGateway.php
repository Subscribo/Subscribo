<?php namespace Omnipay\PayUnity;

use Omnipay\PayUnity\AbstractGateway;

class COPYandPAYGateway extends AbstractGateway
{
    public function acquireToken(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\AcquireCopyAndPayTokenRequest', $parameters);

    }

}
