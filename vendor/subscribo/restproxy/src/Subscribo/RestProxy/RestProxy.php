<?php namespace Subscribo\RestProxy;

use Exception;
use Illuminate\Http\Request;
use Subscribo\RestClient\RestClient;
use Subscribo\RestClient\Factories\SignatureOptionsFactory;
use Subscribo\Exception\Interfaces\ExceptionHandlerInterface;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\RestCommon\AccountIdTransport;

/**
 * Class RestProxy
 *
 * @package Subscribo\RestProxy
 */
class RestProxy {

    /**
     * @var string
     */
    public $uriBase;

    /**
     * @var \Subscribo\RestClient\RestClient
     */
    protected $restClient;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ExceptionHandlerInterface
     */
    protected $exceptionHandler;

    /**
     * @var \Subscribo\RestClient\Factories\SignatureOptionsFactory
     */
    protected $signatureOptionsFactory;


    public function __construct(Request $request, RestClient $client, ExceptionHandlerInterface $exceptionHandler, SignatureOptionsFactory $signatureOptionsFactory, array $settings = null)
    {
        $this->restClient = $client;
        $this->request = $request;
        $this->exceptionHandler = $exceptionHandler;
        $this->signatureOptionsFactory = $signatureOptionsFactory;
        if ($settings)
        {
            $this->setup($settings);
        }
    }

    public function setup(array $settings)
    {
        if (isset($settings['uri'])) {
            $this->uriBase = $settings['uri'];
        }
        if ( ! empty($settings['remote'])) {
            $this->setupRestClient($settings['remote']);
        }
        return $this;
    }

    public function setupRestClient(array $settings)
    {
        if ($this->restClient) {
            $this->restClient->setup($settings);
        } else {
            $this->restClient = new RestClient($settings);
        }
        return $this;
    }

    public function getUriBase()
    {
        return $this->uriBase;
    }

    public function getRemoteUriBase()
    {
        if (empty($this->restClient)) {
            return null;
        }
        return $this->restClient->getUriBase();
    }

    public function getUriParameters()
    {
        if (empty($this->restClient)) {
            return [];
        }
        return $this->restClient->getUriParameters();
    }

    /**
     * @param $uri
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function call($uri)
    {
        try {
            $signatureOptions = $this->signatureOptionsFactory->generate(true);
            $result = $this->restClient->forward($this->request, $uri, $signatureOptions, true);

        } catch (Exception $e) {
            $result = $this->exceptionHandler->handle($e, $this->request);
        }
        return $result;
    }
}
