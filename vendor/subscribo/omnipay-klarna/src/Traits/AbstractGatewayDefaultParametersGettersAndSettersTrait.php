<?php

namespace Omnipay\Klarna\Traits;

trait AbstractGatewayDefaultParametersGettersAndSettersTrait
{
    /**
     * @return string|int
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string
     */
    public function getSharedSecret()
    {
        return $this->getParameter('sharedSecret');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSharedSecret($value)
    {
        return $this->setParameter('sharedSecret', $value);
    }

    /**
     * @return int|string
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * @param int|string $value
     * @return $this
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * Get country of merchant
     * @return int|string
     */
    public function getCountry()
    {
        return $this->getParameter('country');
    }

    /**
     * Set country of merchant
     * @param int|string $value
     * @return $this
     */
    public function setCountry($value)
    {
        return $this->setParameter('country', $value);
    }

    /**
     * Sets Language, Country, and  Currency (if available)
     * @param string $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setLocale($value)
    {
        $normalized = strtr($value, ['_' => '-']);
        $parts = explode('-', $normalized);
        if (empty($parts[1])) {
            throw new \InvalidArgumentException('Locale should be in the format language-country');
        }
        $language = strtolower($parts[0]);
        $country = strtolower($parts[1]);
        $this->setLanguage($language);
        $this->setCountry($country);
        $this->setDefaultCurrency($country);
        return $this;
    }

    /**
     * Sets default currency for country according to current (May 2015) situation
     *
     * @param string $country
     * @return $this
     */
    private function setDefaultCurrency($country)
    {
        switch ($country) {
            case 'dk':
                return $this->setCurrency('dkk');
            case 'no':
                return $this->setCurrency('nok');
            case 'se':
                return $this->setCurrency('sek');
            case 'at':
            case 'be':
            case 'cy':
            case 'de':
            case 'ee':
            case 'es':
            case 'fi':
            case 'fr':
            case 'gr':
            case 'ie':
            case 'it':
            case 'lt':
            case 'lu':
            case 'lv':
            case 'mt':
            case 'nl':
            case 'pt':
            case 'si':
            case 'sk':
                return $this->setCurrency('eur');
            default:
        }
        return $this->setCurrency('');
    }
}
