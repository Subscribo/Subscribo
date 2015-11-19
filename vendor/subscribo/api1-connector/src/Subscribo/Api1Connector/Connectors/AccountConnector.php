<?php

namespace Subscribo\Api1Connector\Connectors;

use Subscribo\Api1Connector\Connectors\AccountSimplifiedConnector;
use Subscribo\RestCommon\SignatureOptions;

/**
 * Class AccountConnector - Extended functionality Account connector
 *
 * @package Subscribo\Api1Connector
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
