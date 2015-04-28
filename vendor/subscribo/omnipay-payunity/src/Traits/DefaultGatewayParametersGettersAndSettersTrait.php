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

    /**
     * @return bool
     */
    public function getRegistrationMode()
    {
        return $this->getParameter('registrationMode');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setRegistrationMode($value)
    {
        return $this->setParameter('registrationMode', $value);
    }

    /**
     * @return string|int
     */
    public function getIdentificationShopperId()
    {
        return $this->getParameter('identificationShopperId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setIdentificationShopperId($value)
    {
        return $this->setParameter('identificationShopperId', $value);
    }

    /**
     * @return string|int
     */
    public function getIdentificationInvoiceId()
    {
        return $this->getParameter('identificationInvoiceId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setIdentificationInvoiceId($value)
    {
        return $this->setParameter('identificationInvoiceId', $value);
    }

    /**
     * @return string|int
     */
    public function getIdentificationBulkId()
    {
        return $this->getParameter('identificationBulkId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setIdentificationBulkId($value)
    {
        return $this->setParameter('identificationBulkId', $value);
    }

}