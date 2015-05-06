<?php

namespace Omnipay\Klarna\Message;

use Omnipay\Klarna\Message\AbstractInvoiceResponse;


class InvoiceAuthorizeResponse extends AbstractInvoiceResponse
{
    public function isSuccessful()
    {
        return '1' === strval($this->getInvoiceStatus());
    }


    public function getInvoiceStatus()
    {
        if (is_array($this->data) and isset($this->data[1])) {
            return $this->data[1];
        }
        return null;
    }


    public function getReservationNumber()
    {
        if (is_array($this->data) and isset($this->data[0])) {
            return $this->data[0];
        }
        return null;
    }

}
