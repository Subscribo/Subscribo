<?php namespace Subscribo\RestClient;

use Exception;
use Subscribo\RestClient\Exceptions\InvalidArgumentException;
use Subscribo\RestClient\Exceptions\ClientErrorException;
use Subscribo\RestClient\Exceptions\ClientErrorHttpException;
use Subscribo\RestClient\Exceptions\RedirectionException;
use Subscribo\RestClient\Exceptions\RedirectionHttpException;
use Subscribo\RestClient\Exceptions\ResponseException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Subscribo\RestClient\Exceptions\TokenConfigurationException;
use Subscribo\RestClient\Exceptions\TokenConfigurationHttpException;
use Subscribo\RestClient\Exceptions\ServerErrorException;
use Subscribo\RestClient\Exceptions\RemoteServerErrorHttpException;
use Subscribo\RestClient\Exceptions\ConnectionException;
use Subscribo\RestClient\Exceptions\ConnectionToRemoteServerHttpException;
use Subscribo\RestClient\Exceptions\InvalidResponseException;
use Subscribo\RestClient\Exceptions\InvalidRemoteServerResponseHttpException;
use Subscribo\RestClient\Factories\ServerRequestExceptionFactory;
use Subscribo\RestCommon\Exceptions\UnauthorizedHttpException;
use Subscribo\RestCommon\RestCommon;
use Subscribo\RestCommon\Signer;
use Subscribo\RestCommon\SignatureOptions;
use Subscribo\Support\Arr;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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

    /**  @var array  */
    protected $uriParameters = [];

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

    public function getUriBase()
    {
        return $this->uriBase;
    }

    public function getUriParameters()
    {
        return $this->uriParameters;
    }

    public function setup(array $settings)
    {
        if (isset($settings['protocol'])) {
            $this->protocol = $settings['protocol'];
        }
        if (isset($settings['host'])) {
            $this->host = $settings['host'];
        }
        if (isset($settings['uri_base'])) {
            $this->uriBase = $settings['uri_base'];
        }
        if (isset($settings['token_ring'])) {
            $this->tokenRing = $settings['token_ring'];
        }
        if ( ! empty($settings['uri_parameters'])) {
            if (is_array($settings['uri_parameters'])) {
                $this->uriParameters = $settings['uri_parameters'];
            } else {
                throw new InvalidArgumentException('RestClient::setup() : uri_parameters should be array if provided');
            }
        }
        $this->client = null;
    }

    /**
     * Forwards the request to given uri stub and format the result as Symfony Response
     *
     * @param Request $request
     * @param string $uriStub
     * @param SignatureOptions|array|null $signatureOptions
     * @param bool $errorToException
     * @return Response
     * @throws Exceptions\ClientErrorHttpException
     * @throws Exceptions\ConnectionToRemoteServerHttpException
     * @throws Exceptions\InvalidRemoteServerResponseHttpException
     * @throws Exceptions\RemoteServerErrorHttpException
     * @throws Exceptions\TokenConfigurationHttpException
     * @throws Exceptions\RedirectionHttpException
     */
    public function forward(Request $request, $uriStub, $signatureOptions = null, $errorToException = true)
    {
        try {
            $callResponse = $this->call(
                $uriStub,
                $request->getMethod(),
                $request->getContent(),
                $request->query->all(),
                $request->headers->all(),
                $signatureOptions,
                $errorToException
            );
        } catch (Exception $e) {
            throw $this->transformExceptions($e);
        }
        $responseContent = $this->extractResponseContent($callResponse);
        $responseStatusCode = $this->extractResponseStatusCode($callResponse);
        $responseStatusMessage = $this->extractResponseStatusMessage($callResponse);
        $responseHeaders = $this->extractResponseHeaders($callResponse);
        $result = new Response($responseContent, $responseStatusCode, $responseHeaders);
        if ($responseStatusMessage) {
            $result->setStatusCode($responseStatusCode, $responseStatusMessage);
        }
        return $result;
    }


    /**
     * Process the call and if possible returns data as an array or throws an exception
     *
     * @param string $uriStub
     * @param string $method
     * @param string|null|array|mixed $content
     * @param array|null $query
     * @param array|null $headers
     * @param SignatureOptions|array|null $signatureOptions
     * @param bool $nullOnClientError
     * @return array|null
     * @throws Exceptions\ConnectionException
     * @throws Exceptions\ResponseException
     * @throws Exceptions\ServerErrorException
     * @throws Exceptions\ClientErrorException
     * @throws Exceptions\ValidationErrorsException
     * @throws Exceptions\TokenConfigurationException
     * @throws Exceptions\ServerRequestException
     */
    public function process($uriStub, $method = 'GET', $content = null, array $query = null, array $headers = null, $signatureOptions = null, $nullOnClientError = false)
    {
        try {
            $callResponse = $this->call($uriStub, $method, $content, $query, $headers, $signatureOptions, true);
            $responseStatusCode = $this->extractResponseStatusCode($callResponse);

            if (($responseStatusCode < 200) or ($responseStatusCode >= 300)){
                throw new ResponseException($responseStatusCode);
            }
            $data = $this->extractResponseData($callResponse, true);

            return $data;

        } catch (ClientErrorException $e) {

            if ($nullOnClientError) {
                return null;
            }
            throw $e;
        }
    }


    /**
     * @param string $uriStub
     * @param string $method
     * @param string|null|array|mixed $content
     * @param array|null $query
     * @param array|null $headers
     * @param SignatureOptions|array|null $signatureOptions
     * @param bool $errorResponseToException
     * @return \GuzzleHttp\Message\FutureResponse|ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|mixed|null
     * @throws Exceptions\ConnectionException
     * @throws Exceptions\ServerErrorException
     * @throws Exceptions\ClientErrorException
     * @throws Exceptions\ValidationErrorsException
     * @throws Exceptions\TokenConfigurationException
     * @throws Exceptions\ServerRequestException
     */
    public function call($uriStub, $method = 'GET', $content = null, array $query = null, array $headers = null, $signatureOptions = null, $errorResponseToException = true)
    {
        $processedHeaders = $this->filterRequestHeaders($headers);

        if ($content and ( ! is_string($content))) {
            $content = json_encode($content);
            $processedHeaders = Arr::withoutKeyCaseInsensitively('Content-Type', $processedHeaders);
            $processedHeaders['Content-Type'] = 'application/json';
        }
        if ($this->tokenRing) {
            $signer = new Signer($this->tokenRing);
            $processedHeaders = $signer->modifyHeaders($processedHeaders, array(), $signatureOptions);
        }
        $uri = $this->prependUriBase($uriStub);

        try {
            $response = $this->callRaw($uri, $method, $content,$query, $processedHeaders);
        } catch (Exception $e) {
            throw new ConnectionException($e);
        }
        if ($errorResponseToException) {
            $responseStatusCode = $this->extractResponseStatusCode($response);
            $this->filterServerRequests($response, $responseStatusCode);
            $this->checkForTokenErrors($response, $responseStatusCode);
            $this->filterErrorResponses($response, $responseStatusCode);
        }
        return $response;
    }

    /**
     * @param string $uriStub
     * @return string
     */
    protected function prependUriBase($uriStub)
    {
        $uri = $this->uriBase;
        $uriStub = ltrim($uriStub, '/');
        if ($uriStub) {
            $uri = trim($uri, '/').'/'.$uriStub;
        }
        $uri = '/'.ltrim($uri, '/');
        return $uri;
    }


    /**
     * Creates and sends request
     *
     * @param string $uri
     * @param string $method
     * @param string|null $body
     * @param array|null $query
     * @param array|null $headers
     * @param array $options
     * @param bool|null $exceptions Whether to throw exceptions on 4xx and 5xx responses
     * @return \GuzzleHttp\Message\FutureResponse|ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|mixed|null
     */
    protected function callRaw($uri, $method = 'GET', $body = null, array $query = null, array $headers = null, array $options = array(), $exceptions = false)
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
        $request = $client->createRequest($method, $uri, $options);
        $response = $client->send($request);
        return $response;
    }

    /**
     * @param ResponseInterface $callResponse
     * @param bool $throwExceptions
     * @return array|null
     * @throws \Exception
     */
    protected function extractResponseData(ResponseInterface $callResponse, $throwExceptions = true)
    {
        try {
            return $callResponse->json(['big_int_strings' => true, 'object' => false]);
        } catch (Exception $e) {
            if ($throwExceptions) {
                throw $e;
            }
        }
        return null;
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
     * @throws Exceptions\InvalidResponseException
     * @throws Exceptions\ServerRequestException
     */
    public function filterServerRequests(ResponseInterface $response, $statusCode = null)
    {
        if ( ! is_int($statusCode)) {
            $statusCode = $this->extractResponseStatusCode($response);
        }
        if ( ! ServerRequestExceptionFactory::isServerRequestResponse($statusCode)) {
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
        try {
            $dataFull = json_decode($responseContent, true);
            $serverRequestException = ServerRequestExceptionFactory::make($statusCode, $dataFull);
        } catch (Exception $e) {
            throw new InvalidResponseException(['originalResponse' => $originalResponse], true, true, $e);
        }
        throw $serverRequestException;
    }

    /**
     * @param ResponseInterface $response
     * @param int|null $statusCode
     * @throws Exceptions\TokenConfigurationException
     */
    public function checkForTokenErrors(ResponseInterface $response, $statusCode = null)
    {
        if ( ! is_int($statusCode)) {
            $statusCode = $this->extractResponseStatusCode($response);
        }
        if (UnauthorizedHttpException::SERVER_STATUS_CODE === $statusCode) {
            throw new TokenConfigurationException($statusCode, $this->extractResponseContent($response));
        }
    }

    /**
     * Throwing Exceptions for 4xx and 5xx responses
     *
     * @param ResponseInterface $response
     * @param int|null $statusCode
     * @throws Exceptions\ServerErrorException
     * @throws Exceptions\ClientErrorException
     * @throws Exceptions\ValidationErrorsException
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
            throw new ServerErrorException($statusCode, $statusMessage, ['originalResponse' => $originalResponse]);
        }

        /* Processing 4xx errors */

        $dataFull = json_decode($responseContent, true);
        $keyName = ClientErrorException::getKey();

        $data = (empty($dataFull[$keyName]) or ( ! is_array($dataFull[$keyName]))) ? array() : $dataFull[$keyName];

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
            $keyName => $data,
        ];
        $filteredHeaders = $this->extractResponseHeaders($response);
        if (empty($data['validationErrors'])) {
            $e = new ClientErrorException($statusCode, $message, $exceptionData, $exceptionCode, null, $filteredHeaders);
        } else {
            $e = new ValidationErrorsException($statusCode, $message, $exceptionData, $exceptionCode, null, $filteredHeaders);
        }
        if ($statusMessage) {
            $e->setStatusMessage($statusMessage);
        }
        throw $e;
    }

    /**
     * @param array|null $headers
     * @return array
     */
    public function filterRequestHeaders(array $headers = null)
    {
        if (empty($headers)) {
            return array();
        }
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
            'Content-Language',
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

    /**
     * @param Exception|ClientErrorException|ServerErrorException|TokenConfigurationException|ConnectionException|InvalidResponseException $originalException
     * @return Exception|ClientErrorHttpException|ConnectionToRemoteServerHttpException|InvalidRemoteServerResponseHttpException|RedirectionHttpException|RemoteServerErrorHttpException|TokenConfigurationHttpException
     */
    public function transformExceptions(Exception $originalException)
    {
        if ($originalException instanceof ClientErrorException) {
            return new ClientErrorHttpException($originalException->getStatusCode(), $originalException->getMessage(), $originalException->getData(), $originalException->getCode(), $originalException, $originalException->getHeaders());
        }
        if ($originalException instanceof ServerErrorException) {
            return new RemoteServerErrorHttpException($originalException);
        }
        if ($originalException instanceof TokenConfigurationException) {
            return new TokenConfigurationHttpException($originalException);
        }
        if ($originalException instanceof ConnectionException) {
            return new ConnectionToRemoteServerHttpException($originalException);
        }
        if ($originalException instanceof InvalidResponseException) {
            return new InvalidRemoteServerResponseHttpException($originalException);
        }
        if ($originalException instanceof RedirectionException) {
            return new RedirectionHttpException($originalException);
        }
        return $originalException;
    }

}
