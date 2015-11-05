<?php

namespace Subscribo\ApiClientAuth\Connectors;

use Subscribo\ApiClientAuth\Connectors\AccountSimplifiedConnector;
use Subscribo\RestCommon\SignatureOptions;

/**
 * Class AccountConnector - Extended functionality Account connector
 *
 * @package Subscribo\ApiClientAuth
 */
class AccountConnector extends AccountSimplifiedConnector
{
    /**
     * @param int $id
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getAddress($id = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/address'.($id ? '/'.$id : ''), 'GET', null, null, null, $signatureOptions, false);

        return $responseData['result'];
    }

    protected function initialize()
    {
        //Overriding (back to empty) overridden method in order that SignatureOptionsFactory have default defaults
    }
}
