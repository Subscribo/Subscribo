<?php

namespace Subscribo\Api1Connector\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;

/**
 * Class BusinessConnector
 *
 * @package Subscribo\Api1Connector
 */
class BusinessConnector extends AbstractConnector
{
    /**
     * @param int|string|null $id
     * @param array|null $query
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getProduct($id = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/product/'.$id, 'GET', null, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    /**
     * @param int $limit
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getAvailableDeliveries($limit = 5, $signatureOptions = true)
    {
        $query = ['available' => $limit];

        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/delivery', 'GET', null, $query, null, $signatureOptions);

        return $responseData['collection'];
    }

    /**
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getSubscriptionPeriods($signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/period', 'GET', null, null, null, $signatureOptions);

        return $responseData['result'];
    }

    /**
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getUsualDeliveryWindowTypes($signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $query = ['usual' => true];

        $responseData = $this->restClient->process('business/delivery_window_type', 'GET', null, $query, null, $signatureOptions);

        return $responseData['collection'];
    }

    /**
     * @param array|null $content
     * @param array|null $query
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postOrder(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/order', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    /**
     * @param array|null $content
     * @param array|null $query
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postSubscription(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/subscription', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }

    /**
     * @param array|null $content
     * @param array|null $query
     * @param \Subscribo\RestCommon\SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postMessage(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/message', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }
}
