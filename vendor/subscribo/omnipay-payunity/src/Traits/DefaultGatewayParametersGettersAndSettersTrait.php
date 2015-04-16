<?php namespace Omnipay\PayUnity\Traits;

trait DefaultGatewayParametersGettersAndSettersTrait
{
    public function getSecuritySender()
    {
        return $this->getParameter('securitySender');
    }

    public function setSecuritySender($value)
    {
        return $this->setParameter('securitySender', $value);
    }

    public function getTransactionChannel()
    {
        return $this->getParameter('transactionChannel');
    }

    public function setTransactionChannel($value)
    {
        return $this->setParameter('transactionChannel', $value);
    }

    public function getTransactionMode()
    {
        return $this->getParameter('transactionMode');
    }

    public function setTransactionMode($value)
    {
        return $this->setParameter('transactionMode', $value);
    }

    public function getUserLogin()
    {
        return $this->getParameter('userLogin');
    }

    public function setUserLogin($value)
    {
        return $this->setParameter('userLogin', $value);
    }

    public function getUserPwd()
    {
        return $this->getParameter('userPwd');
    }

    public function setUserPwd($value)
    {
        return $this->setParameter('userPwd', $value);
    }

    public function setTestMode($value)
    {
        if ($value) {
            $currentTransactionModeNormalized = trim(strtoupper($this->getTransactionMode()));
            if (empty($currentTransactionModeNormalized) or ($currentTransactionModeNormalized === 'LIVE')) {
                $this->setTransactionMode('INTEGRATOR_TEST');
            }
        }
        return parent::setTestMode($value);
    }

}