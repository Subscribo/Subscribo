<?php

namespace Subscribo\Webshop\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;

class TransactionConnector extends AbstractConnector
{
    public function getGateway($id = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('transaction/gateway/'.$id, 'GET', null, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    public function postCharge(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('transaction/charge', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }
}
