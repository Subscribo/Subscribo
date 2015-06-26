<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractPostRequest;

/**
 * Class GenericPostRequest
 *
 * @package Omnipay\PayUnity
 */
class GenericPostRequest extends AbstractPostRequest
{
    /**
     * This is to be redefined in particular PostRequest messages
     *
     * @var string
     */
    protected $defaultPaymentType = 'DB';

    protected $defaultPaymentMethod = 'CC';

    public function getData()
    {
        $result = $this->prepareData();

        $result['PAYMENT.CODE'] = $this->getPaymentCode() ?: $this->assemblePaymentCode();

        $result = $this->addCardReference($result);

        return $result;
    }

    /**
     * @return string|null
     */
    public function getPaymentCode()
    {
        return $this->getParameter('paymentCode');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPaymentCode($value)
    {
        return $this->setParameter('paymentCode', $value);
    }

    /**
     * @return string
     */
    protected function assemblePaymentCode()
    {
        $method = $this->getPaymentMethod() ?: $this->defaultPaymentMethod;
        $type = $this->getPaymentType() ?: $this->defaultPaymentType;

        return $method.'.'.$type;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addCardReference(array $data)
    {
        $cardReference = $this->getCardReference();

        if ($cardReference) {
            $decoded = base64_decode($cardReference, true);
            $parsed = json_decode($decoded, true, 2, JSON_BIGINT_AS_STRING);
            $data['ACCOUNT.REGISTRATION'] = $parsed['registration'];
            $data['PAYMENT.CODE'] = $parsed['code'];
        }

        return $data;
    }
}
