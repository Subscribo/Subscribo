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

    /** @var  string $accessToken */
    protected $accessToken;

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
        if (array_key_exists('access_token', $settings)) {
            $this->accessToken = $settings['access_token'];
        }
        $this->client = null;
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
     * @param string $uri
     * @param string $method
     * @param array $query
     * @param array $headers
     * @param null $body
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|mixed|null
     */
    public function call($uri, $method = 'GET', $query = array(), $headers = array(), $body = null)
    {
        $client = $this->client();
        $processedHeaders = $this->filterRequestHeaders($headers);
        $processedHeaders[RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME] = $this->accessToken;
        $options = [
            'headers' => $processedHeaders,
            'exceptions' => false,
        ];
        if ($body) {
            $options['body'] = $body;
        }
        if ($query) {
            $options['query'] = $query;
        }
        if ($uri) {
            $uri = '/'.trim($this->uriBase).'/'.ltrim($uri,'/');
        }
        $request = $client->createRequest($method, $uri, $options);
        $response = $client->send($request);
        return $response;
    }

    /**
     * Process the call and format the result as Symfony Response
     *
     * @param string $uri
     * @param string $method
     * @param array $query
     * @param array $headers
     * @param null $body
     * @return Response
     * @throws Exceptions\TokenConfigurationHttpException
     * @throws Exceptions\ClientErrorHttpException
     * @throws Exceptions\RemoteServerErrorHttpException
     */
    public function process($uri, $method = 'GET', $query = array(), $headers = array(), $body = null)
    {
        $callResponse = $this->call($uri, $method, $query, $headers, $body);
        $responseContent = $this->extractResponseContent($callResponse);
        $responseStatusCode = $this->extractResponseStatusCode($callResponse);
        $responseStatusMessage = $this->extractResponseStatusMessage($callResponse);
        $responseHeaders = $this->extractResponseHeaders($callResponse);
        $result = new Response($responseContent, $responseStatusCode, $responseHeaders);
        if ($responseStatusMessage) {
            $result->setStatusCode($responseStatusCode, $responseStatusMessage);
        }
        $this->checkForTokenErrors($result);
        $this->filterErrorResponses($responseContent, $responseStatusCode, $responseHeaders, $responseStatusMessage);
        return $result;
    }

    /**
     * @param Response $response
     * @throws Exceptions\TokenConfigurationHttpException
     */
    public function checkForTokenErrors(Response $response)
    {
        $statusCode = $response->getStatusCode();
        if ((NoAccessTokenHttpException::SERVER_STATUS_CODE === $statusCode)
            or (InvalidAccessTokenHttpException::SERVER_STATUS_CODE === $statusCode)) {
            throw new TokenConfigurationHttpException($statusCode, $response->getContent());
        }
    }

    /**
     * @param Response $response
     * @throws Exceptions\RemoteServerErrorHttpException
     */
    public function checkForRemoteServerErrors(Response $response)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 500) {
            throw new RemoteServerErrorHttpException($statusCode, $response->getContent());
        }
    }

    /**
     * Throwing Exceptions for 4xx and 5xx responses
     *
     * @param $content
     * @param $statusCode
     * @param $headers
     * @param $statusMessage
     * @throws Exceptions\ClientErrorHttpException
     * @throws Exceptions\RemoteServerErrorHttpException
     */
    public function filterErrorResponses($content, $statusCode, $headers, $statusMessage)
    {
        $statusCode = intval($statusCode);
        if (($statusCode < 400)) {
            return;
        }
        if (($statusCode >= 500)) {
            throw new RemoteServerErrorHttpException($statusCode, $content);
        }
        $data = json_decode($content, true);

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
        $exceptionData = ['output' => $data];
        $e = new ClientErrorHttpException($statusCode, $message, $exceptionData, $exceptionCode, null, $headers);
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
            'Content-Type', 'Content-Encoding',
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
        return $response->getStatusCode();
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
