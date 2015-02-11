<?php namespace Subscribo\RestProxy;

use Exception;
use Illuminate\Http\Request;
use Subscribo\RestClient\RestClient;
use Subscribo\Exception\Interfaces\ExceptionHandlerInterface;

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


    public function __construct(Request $request, RestClient $client, ExceptionHandlerInterface $exceptionHandler, array $settings = null)
    {
        $this->restClient = $client;
        $this->request = $request;
        $this->exceptionHandler = $exceptionHandler;
        if ($settings)
        {
            $this->setup($settings);
        }
    }

    public function setup(array $settings)
    {
        if (array_key_exists('uri', $settings)) {
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
            $result = $this->restClient->forward($this->request, $uri, null, true);

        } catch (Exception $e) {
            $result = $this->exceptionHandler->handle($e, $this->request);
        }
        return $result;
    }
}
