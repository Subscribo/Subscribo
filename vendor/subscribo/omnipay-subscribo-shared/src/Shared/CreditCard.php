<?php namespace Subscribo\Omnipay\Shared;


use Omnipay\Common\CreditCard as Base;

/**
 * Class CreditCard extends Omnipay Common CreditCard and adds some attributes
 *
 * Added attributes:
 *
 * * identificationDocumentNumber
 * * identificationDocumentType
 * * socialSecurityNumber - deprecated - use nationalIdentificationNumber instead (now only an alias for nationalIdentificationNumber)
 * * nationalIdentificationNumber - country-specific identifier of a person or a company,
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
    const MODE_FILTER_EMPTY_VALUES = 'filter_empty_values';
    const MODE_FILTER_EMPTY_WHEN_STRING_VALUES = 'filter_empty_when_string_values';


    /**
     * Returns those shipping contact parameters which are different for shipping and billing
     * Keys are camel cased parameter names without shipping prefix, values are shipping contact parameter values
     *
     * @param string $mode Filtering mode (if any): By default this methods filters out values, which are empty when converted to string
     * @return array
     */
    public function getShippingContactDifferences($mode = self::MODE_FILTER_EMPTY_WHEN_STRING_VALUES)
    {
        $difference = [];
        foreach ($this->getContactParameterNames() as $parameterName)
        {
            $billingGetter = 'getBilling'.ucfirst($parameterName);
            $shippingGetter = 'getShipping'.ucfirst($parameterName);
            $billingValue = $this->$billingGetter();
            $shippingValue = $this->$shippingGetter();
            if ($billingValue !== $shippingValue) {
                $difference[$parameterName] = $shippingValue;
            }
        }
        if ($mode === self::MODE_FILTER_EMPTY_WHEN_STRING_VALUES) {
            $result = array_filter($difference, 'strlen');
        } elseif ($mode === self::MODE_FILTER_EMPTY_VALUES) {
            $result = array_filter($difference);
        }
        else {
            $result = $difference;
        }
        return $result;
    }
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
     * @return int|string|null
     */
    public function getNationalIdentificationNumber()
    {
        return $this->getParameter('nationalIdentificationNumber');
    }

    /**
     * @param int|string $value
     * @return $this
     */
    public function setNationalIdentificationNumber($value)
    {
        return $this->setParameter('nationalIdentificationNumber', $value);
    }

    /**
     * Alias for getNationalIdentificationNumber()
     *
     * @deprecated use getNationalIdentificationNumber() instead
     * @return int|string
     */
    public function getSocialSecurityNumber()
    {
        return $this->getNationalIdentificationNumber();
    }
    
    /**
     * Alias for setNationalIdentificationNumber()
     *
     * @deprecated use setNationalIdentificationNumber() instead
     * @param int|string $value
     * @return $this
     */
    public function setSocialSecurityNumber($value)
    {
        return $this->setNationalIdentificationNumber($value);
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

    /**
     * Returns list of parameters, which are part of contact information and have a billing and shipping counterpart
     * Name is not included, as it is implemented as a compound of firstName and lastName
     * @return array
     */
    protected function getContactParameterNames()
    {
        return [
            'title',
            'firstName',
            'lastName',
            'company',
            'address1',
            'address2',
            'city',
            'postcode',
            'state',
            'country',
            'phone',
            'fax',
            'mobile',
            'salutation'
        ];
    }
}
