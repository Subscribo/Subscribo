<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericPostRequest;


/**
 * Class PostVoidRequest
 *
 * Request for transaction reversal
 *
 * @package Omnipay\PayUnity
 */
class PostVoidRequest extends GenericPostRequest
{
    protected $defaultPaymentType = 'RV';

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
            $paymentCode = $parsed['code'];
            $paymentType = $this->getPaymentType() ?: $this->defaultPaymentType;
            $data['PAYMENT.CODE'] = $this->changePaymentTypeInCode($paymentCode, $paymentType);
        }

        return $data;
    }

    /**
     * Changes payment type part in payment code
     *
     * @param string $paymentCode
     * @param string $paymentType
     * @return string
     */
    protected function changePaymentTypeInCode($paymentCode, $paymentType)
    {
        $parts = explode('.', $paymentCode);
        $paymentMethod = reset($parts);

        return $paymentMethod.'.'.$paymentType;
    }
}
