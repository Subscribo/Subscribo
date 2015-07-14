<?php

namespace Subscribo\Webshop\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;

class PaymentConnector extends AbstractConnector
{
    public function getMethod($id = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('payment/method/'.$id, 'GET', null, $query, null, $signatureOptions);

        return $responseData['result'];
    }
}
