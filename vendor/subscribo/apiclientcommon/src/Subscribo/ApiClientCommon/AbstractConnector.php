<?php namespace Subscribo\ApiClientCommon;

use Subscribo\RestClient\RestClient;
use Subscribo\RestClient\Factories\SignatureOptionsFactory;
use Subscribo\RestCommon\SignatureOptions;

/**
 * Abstract Class AbstractConnector
 *
 * @package Subscribo\ApiClientCommon
 */
abstract class AbstractConnector
{
    /**
     * @var \Subscribo\RestClient\RestClient
     */
    protected $restClient;

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
     * @param bool $signatureOptions
     * @return SignatureOptions
     */
    protected function processSignatureOptions($signatureOptions = true)
    {
        if ($signatureOptions instanceof SignatureOptions) {
            return $signatureOptions;
        }
        return $this->signatureOptionsFactory->generate($signatureOptions);
    }

}
