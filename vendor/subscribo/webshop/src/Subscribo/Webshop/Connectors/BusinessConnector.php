<?php

namespace Subscribo\Webshop\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;

class BusinessConnector extends AbstractConnector
{
    public function getProduct($id = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/product/'.$id, 'GET', null, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    public function getGateway($id = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/gateway/'.$id, 'GET', null, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    public function postOrder(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/order', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }
}
