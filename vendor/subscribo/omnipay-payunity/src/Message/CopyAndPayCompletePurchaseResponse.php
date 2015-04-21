<?php namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;

class CopyAndPayCompletePurchaseResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        if (empty($this->data['transaction']['processing']['result'])) {
            return false;
        }
        if ('ACK' === $this->data['transaction']['processing']['result']) {
            return true;
        }
        return false;
    }

    public function isWaiting()
    {
        if (empty($this->data['transaction']['processing']['result'])) {
            return false;
        }
        if ('WAITING FOR SHOPPER' === $this->data['transaction']['processing']['result']) {
            return true;
        }
        return false;
    }

    public function getTransactionReference()
    {
        if ( ! empty($this->data['transaction']['identification']['uniqueId'])) {
            return $this->data['transaction']['identification']['uniqueId'];
        }
        return null;
    }

    public function getMessage()
    {
        if ( ! empty($this->data['transaction']['processing']['return']['message'])) {
            return $this->data['transaction']['processing']['return']['message'];
        }
        if ( ! empty($this->data['errorMessage'])) {
            return $this->data['errorMessage'];
        }
        return null;
    }


    public function getCode()
    {
        if ( ! empty($this->data['transaction']['processing']['return']['code'])) {
            return $this->data['transaction']['processing']['return']['code'];
        }
        return null;
    }
}
