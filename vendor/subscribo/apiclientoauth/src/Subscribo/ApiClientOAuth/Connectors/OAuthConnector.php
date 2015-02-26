<?php namespace Subscribo\ApiClientOAuth\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;

/**
 * Class OAuthConnector
 *
 * @package Subscribo\ApiClientOAuth
 */
class OAuthConnector extends AbstractConnector
{
    public function getConfig($driver, array $query = null, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process('oauth/config/'.$driver, 'GET', null, $query, null, $signatureOptions, false);

        return $responseData['result'][$driver];
    }

}
