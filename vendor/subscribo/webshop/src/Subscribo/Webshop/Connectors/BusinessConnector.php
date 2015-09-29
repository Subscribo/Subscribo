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


    public function getAvailableDeliveries($deliveryPlanId, $limit = 5, $signatureOptions = true)
    {
        $query = ['available_only' => true, 'delivery_plan_id' => $deliveryPlanId, 'limit' => $limit];

        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/delivery', 'GET', null, $query, null, $signatureOptions);

        return $responseData['collection'];
    }


    public function getSubscriptionPeriods($signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/period', 'GET', null, null, null, $signatureOptions);

        return $responseData['result'];
    }


    public function getUsualDeliveryWindowTypes($signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $query = ['usual' => true];

        $responseData = $this->restClient->process('business/delivery_window_type', 'GET', null, $query, null, $signatureOptions);

        return $responseData['collection'];
    }


    public function postOrder(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/order', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }


    public function postSubscription(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/subscription', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }


    public function postMessage(array $content = null, array $query = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('business/message', 'POST', $content, $query, null, $signatureOptions);

        return $responseData['result'];
    }
}
