<?php

namespace Subscribo\ApiClientAuth\Connectors;

use Subscribo\ApiClientAuth\Connectors\AccountSimplifiedConnector;
use Subscribo\RestClient\RestClient;
use Subscribo\RestClient\Factories\SignatureOptionsFactory;
use Subscribo\RestCommon\SignatureOptions;

/**
 * Class AccountConnector - Extended functionality Account connector
 *
 * @package Subscribo\ApiClientAuth
 */
class AccountConnector extends AccountSimplifiedConnector
{
    /**
     * @var \Subscribo\RestClient\Factories\SignatureOptionsFactory
     */
    protected $signatureOptionsFactory;

    public function __construct(RestClient $restClient, SignatureOptionsFactory $signatureOptionsFactory)
    {
        $this->restClient = $restClient;
        $this->signatureOptionsFactory = $signatureOptionsFactory;
    }

    /**
     * @param int $id
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getAddress($id = null, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/address'.($id ? '/'.$id : ''), 'GET', null, null, null, $signatureOptions, true);

        return $responseData['result'];
    }

    /**
     * @param bool $signatureOptions
     * @return SignatureOptions
     */
    protected function processSignatureOptions($signatureOptions = true)
    {
        return $this->signatureOptionsFactory->generate($signatureOptions);
    }
}
