<?php namespace Subscribo\RestClient;

use Subscribo\RestClient\Exceptions\ClientErrorHttpException;
use Subscribo\RestCommon\RestCommon;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Subscribo\Support\Arr;
use Subscribo\RestCommon\Exceptions\NoAccessTokenHttpException;
use Subscribo\RestCommon\Exceptions\InvalidAccessTokenHttpException;
use Subscribo\RestClient\Exceptions\TokenConfigurationHttpException;
use Subscribo\RestClient\Exceptions\RemoteServerErrorHttpException;
use Subscribo\RestClient\Exceptions\ConnectionToRemoteServerHttpException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ConnectException;
use Subscribo\RestCommon\Signer;

/**
 * Class RestClient
 *
 * @package Subscribo\RestClient
 */
class RestClient {

    /** @var  string $protocol */
    protected $protocol;

    /** @var  string $host */
    protected $host;

    /** @var  string $uriBase */
    protected $uriBase;

    /** @var string|array */
    protected $tokenRing;

    /** @var  Client $client */
    protected $client;

    public function __construct(array $settings = array())
    {
        if ($settings) {
            $this->setup($settings);
        }
    }

    public function setup(array $settings)
    {
        if (array_key_exists('protocol', $settings)) {
            $this->protocol = $settings['protocol'];
        }
        if (array_key_exists('host', $settings)) {
            $this->host = $settings['host'];
        }
        if (array_key_exists('uri_base', $settings)) {
            $this->uriBase = trim($settings['uri_base'],'/');
        }
        if (array_key_exists('token_ring', $settings)) {
            $this->tokenRing = $settings['token_ring'];
        }
        $this->client = null;
    }

    /**
     * Process the call and format the result as Symfony Response
     *
     * @param string $uri
     * @param string $method
     * @param array|null $query
     * @param array $headers
     * @param string|null $body
     * @return Response
     * @throws \GuzzleHttp\Exception\TransferException
     * @throws Exceptions\TokenConfigurationHttpException
     * @throws Exceptions\ClientErrorHttpException
     * @throws Exceptions\RemoteServerErrorHttpException
     * @throws Exceptions\ConnectionToRemoteServerHttpException
     */
    public function process($uri, $method = 'GET', array $query = null, array $headers = array(), $body = null)
    {
        try {
            $callResponse = $this->call($uri, $method, $query, $headers, $body);
        } catch (ConnectException $e) {
            throw new ConnectionToRemoteServerHttpException($e);
        }
        $responseStatusCode = $this->extractResponseStatusCode($callResponse);

        $this->checkForTokenErrors($callResponse, $responseStatusCode);
        $this->filterErrorResponses($callResponse, $responseStatusCode);

        $responseContent = $this->extractResponseContent($callResponse);
        $responseStatusMessage = $this->extractResponseStatusMessage($callResponse);
        $responseHeaders = $this->extractResponseHeaders($callResponse);
        $result = new Response($responseContent, $responseStatusCode, $responseHeaders);
        if ($responseStatusMessage) {
            $result->setStatusCode($responseStatusCode, $responseStatusMessage);
        }
        return $result;
    }


    /**
     * @param string $uri
     * @param string $method
     * @param array|null $query
     * @param array $headers
     * @param string|null $body
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|mixed|null
     * @throws \GuzzleHttp\Exception\TransferException
     */
    public function call($uri, $method = 'GET', array $query = null, array $headers = array(), $body = null)
    {
        $processedHeaders = $this->filterRequestHeaders($headers);
        if ($this->tokenRing) {
            $signer = new Signer($this->tokenRing);
            $processedHeaders = $signer->modifyHeaders($processedHeaders);
        }
        if ($uri) {
            $uri = trim($this->uriBase, '/').'/'.ltrim($uri, '/');
            $uri = '/'.ltrim($uri, '/');
        }
        $response = $this->callRaw($method, $uri, $query, $processedHeaders, $body);
        return $response;
    }

    /**
     * Creates and sends request
     *
     * @param string $method
     * @param string|null $url
     * @param array|null $query
     * @param array|null $headers
     * @param string|null $body
     * @param array $options
     * @param bool|null $exceptions Whether to throw exceptions on 4xx and 5xx responses
     * @return \GuzzleHttp\Message\FutureResponse|ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|mixed|null
     * @throws \GuzzleHttp\Exception\TransferException
     */
    protected function callRaw($method = 'GET', $url = null, array $query = null, array $headers = null, $body = null, array $options = array(), $exceptions = false)
    {
        if ( ! is_null($exceptions)) {
            $options['exceptions'] = $exceptions;
        }
        if ( ! is_null($headers)) {
            $options['headers'] = $headers;
        }
        if ( ! is_null($body)) {
            $options['body'] = $body;
        }
        if ( ! is_null($query)) {
            $options['query'] = $query;
        }
        $client = $this->client();
        $request = $client->createRequest($method, $url, $options);
        $response = $client->send($request);
        return $response;
    }

    /**
     * @return Client
     */
    protected function client()
    {
        if ($this->client) {
            return $this->client;
        }
        $baseUrl = new \GuzzleHttp\Url($this->protocol, $this->host, null, null, null, $this->uriBase);
        $this->client = new Client(['base_url' => $baseUrl]);
        return $this->client;
    }

    /**
     * @param ResponseInterface $response
     * @param int|null $statusCode
     * @throws Exceptions\TokenConfigurationHttpException
     */
    public function checkForTokenErrors(ResponseInterface $response, $statusCode = null)
    {
        if ( ! is_int($statusCode)) {
            $statusCode = $this->extractResponseStatusCode($response);
        }
        if ((NoAccessTokenHttpException::SERVER_STATUS_CODE === $statusCode)
            or (InvalidAccessTokenHttpException::SERVER_STATUS_CODE === $statusCode)) {
            throw new TokenConfigurationHttpException($statusCode, $this->extractResponseContent($response));
        }
    }


    /**
     * Throwing Exceptions for 4xx and 5xx responses
     *
     * @param ResponseInterface $response
     * @param int|null $statusCode
     * @throws Exceptions\ClientErrorHttpException
     * @throws Exceptions\RemoteServerErrorHttpException
     */
    public function filterErrorResponses(ResponseInterface $response, $statusCode = null)
    {
        if ( ! is_int($statusCode)) {
            $statusCode = $this->extractResponseStatusCode($response);
        }
        if (($statusCode < 400)) {
            return;
        }
        $responseContent = $this->extractResponseContent($response);
        $statusMessage = $this->extractResponseStatusMessage($response);
        $originalHeaders = $response->getHeaders();
        $originalResponse = [
            'content' => $responseContent,
            'statusCode' => $statusCode,
            'statusMessage' => $statusMessage,
            'headers' => $originalHeaders,
        ];
        if (($statusCode >= 500)) {
            throw new RemoteServerErrorHttpException(['originalResponse' => $originalResponse]);
        }

        /* Processing 4xx errors */

        $data = json_decode($responseContent, true);

        $message = (isset($data['message']) and is_string($data['message'])) ? $data['message'] : null;

        $exceptionCode = (isset($data['metaData']['exceptionCode']) and is_numeric($data['metaData']['exceptionCode']))
            ? intval($data['metaData']['exceptionCode']) : 0;

        if ( ! empty(RestCommon::$responseContentItemsToRemove[$statusCode])) {
            foreach (RestCommon::$responseContentItemsToRemove[$statusCode] as $keyToRemove) {
                unset($data[$keyToRemove]);
            }
        }
        if ( ! empty(RestCommon::$responseContentItemsToRemove['anyStatus'])) {
            foreach (RestCommon::$responseContentItemsToRemove['anyStatus'] as $keyToRemove) {
                unset($data[$keyToRemove]);
            }
        }

        $exceptionData = [
            'originalResponse' => $originalResponse,
            'output' => $data,
        ];
        $filteredHeaders = $this->extractResponseHeaders($response);

        $e = new ClientErrorHttpException($statusCode, $message, $exceptionData, $exceptionCode, null, $filteredHeaders);
        if ($statusMessage) {
            $e->setStatusMessage($statusMessage);
        }
        throw $e;
    }

    /**
     * @param array $headers
     * @return array
     */
    public function filterRequestHeaders(array $headers)
    {
        $allowedRequestHeaders = [
            'Accept', 'Accept-Charset', 'Accept-Encoding', 'Accept-Language', 'Accept-Datetime',
            'Content-Length', 'Content-MD5', 'Content-Type',
            'Date', 'Expect', 'From',
            'If-Match', 'If-Modified-Since', 'If-None-Match', 'If-Range', 'If-Unmodified-Since',
            'Range', 'TE', 'User-Agent', 'Upgrade', 'Via', 'Warning'
        ];
        $result = Arr::filterCaseInsensitively($allowedRequestHeaders, $headers);
        return $result;
    }

    /**
     * @param array $headers
     * @return array
     */
    public function filterResponseHeaders(array $headers)
    {
        $allowedResponseHeaders = [
            'Content-Type',
            'Date',
        ];
        $result = Arr::filterCaseInsensitively($allowedResponseHeaders, $headers);
        return $result;
    }

    /**
     * @param ResponseInterface $response
     * @return string
     */
    public function extractResponseContent(ResponseInterface $response)
    {
        return $response->getBody()->__toString();
    }

    /**
     * @param ResponseInterface $response
     * @return int
     */
    public function extractResponseStatusCode(ResponseInterface $response)
    {
        return intval($response->getStatusCode());
    }

    public function extractResponseStatusMessage(ResponseInterface $response)
    {
        return $response->getReasonPhrase();
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    public function extractResponseHeaders(ResponseInterface $response)
    {
        $originalHeaders = $response->getHeaders();
        $filteredHeaders = $this->filterResponseHeaders($originalHeaders);
        return $filteredHeaders;
    }

}
