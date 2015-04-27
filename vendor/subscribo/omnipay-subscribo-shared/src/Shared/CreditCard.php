<?php namespace Subscribo\Omnipay\Shared;


use Omnipay\Common\CreditCard as Base;

/**
 * Class CreditCard extends Omnipay Common CreditCard and adds some attributes
 *
 * Added attributes:
 *
 * * identificationDocumentNumber
 * * identificationDocumentType
 * * socialSecurityNumber
 * * mobile
 * * salutation
 * * billingMobile
 * * billingSalutation
 * * shippingMobile
 * * shippingSalutation
 * 
 * @package Subscribo\OmnipaySubscriboShared
 */
class CreditCard extends Base
{
    /**
     * @return int|string
     */
    public function getIdentificationDocumentNumber()
    {
        return $this->getParameter('identificationDocumentNumber');
    }
    
    /**
     * @param int|string $value
     * @return $this
     */
    public function setIdentificationDocumentNumber($value)
    {
        return $this->setParameter('identificationDocumentNumber', $value);
    }

    /**
     * @return string
     */
    public function getIdentificationDocumentType()
    {
        return $this->getParameter('identificationDocumentType');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setIdentificationDocumentType($value)
    {
        return $this->setParameter('identificationDocumentType', $value);
    }

    /**
     * @return int|string
     */
    public function getSocialSecurityNumber()
    {
        return $this->getParameter('socialSecurityNumber');
    }
    
    /**
     * @param int|string $value
     * @return $this
     */
    public function setSocialSecurityNumber($value)
    {
        return $this->setParameter('socialSecurityNumber', $value);
    }

    /**
     * @return int|string
     */
    public function getMobile()
    {
        return $this->getBillingMobile();
    }

    /**
     * @param int|string $value
     * @return $this
     */
    public function setMobile($value)
    {
        $this->setBillingMobile($value);
        $this->setShippingMobile($value);

        return $this;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->getBillingSalutation();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSalutation($value)
    {
        $this->setBillingSalutation($value);
        $this->setShippingSalutation($value);

        return $this;
    }



    /**
     * @return int|string
     */
    public function getBillingMobile()
    {
        return $this->getParameter('billingMobile');
    }

    /**
     * @param int|string $value
     * @return $this
     */
    public function setBillingMobile($value)
    {
        return $this->setParameter('billingMobile', $value);
    }

    /**
     * @return string
     */
    public function getBillingSalutation()
    {
        return $this->getParameter('billingSalutation');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setBillingSalutation($value)
    {
        return $this->setParameter('billingSalutation', $value);
    }

    /**
     * @return int|string
     */
    public function getShippingMobile()
    {
        return $this->getParameter('shippingMobile');
    }

    /**
     * @param int|string $value
     * @return $this
     */
    public function setShippingMobile($value)
    {
        return $this->setParameter('shippingMobile', $value);
    }

    /**
     * @return string
     */
    public function getShippingSalutation()
    {
        return $this->getParameter('shippingSalutation');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingSalutation($value)
    {
        return $this->setParameter('shippingSalutation', $value);
    }
}
