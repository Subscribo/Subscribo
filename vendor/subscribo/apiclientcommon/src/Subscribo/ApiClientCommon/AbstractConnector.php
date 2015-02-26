<?php namespace Subscribo\ApiClientCommon;

use Subscribo\RestClient\RestClient;

abstract class AbstractConnector
{
    /**
     * @var \Subscribo\RestClient\RestClient
     */
    protected $restClient;

    public function __construct(RestClient $restClient)
    {
        $this->restClient = $restClient;
    }

}
