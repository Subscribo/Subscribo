<?php

namespace Subscribo\Api1Connector\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;

/**
 * Class TransactionConnector
 *
 * @package Subscribo\Api1Connector
 */
class TransactionConnector extends AbstractConnector
{
    /**
     * @param string|null $id
     * @param array|null $query
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getGateway($id = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('transaction/gateway/'.$id, 'GET', null, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    /**
     * @param array|null $content
     * @param array|null $query
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postCharge(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('transaction/charge', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }
}
