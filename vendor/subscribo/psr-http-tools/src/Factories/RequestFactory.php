<?php namespace Subscribo\PsrHttpTools\Factories;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Phly\Http\Request;
use Phly\Http\Stream;
use Subscribo\PsrHttpTools\Factories\UriFactory;

/**
 * Class RequestFactory
 *
 * Factory for making PSR-7 compliant requests
 *
 * @package Subscribo\PsrHttpTools
 */
class RequestFactory
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';

    const CONTENT_CHARSET_UTF_8 = 'UTF-8';

    const DEFAULT_CONTENT_TYPE = self::CONTENT_TYPE_FORM;
    const DEFAULT_CONTENT_CHARSET = self::CONTENT_CHARSET_UTF_8;

    /**
     * Makes PSR-7 compliant Request object from provided parameters
     *
     * @param UriInterface|string $uri
     * @param null|string|array $data
     * @param array $queryParameters
     * @param array $headers
     * @param null|string $method
     * @param null|string $contentType
     * @param null|string $contentCharset
     * @return RequestInterface
     * @throws InvalidArgumentException
     */
    public static function make($uri, $data = null, array $queryParameters = [], array $headers = [], $method = null, $contentType = null, $contentCharset = null)
    {
        if (is_null($method)) {
            $method = is_null($data) ? static::METHOD_GET : static::METHOD_POST;
        }
        $uri = UriFactory::make($uri, $queryParameters);
        $stream = new Stream('php://memory', 'r+');
        $request = new Request($uri, $method, $stream, $headers);

        $contentType = $contentType ?: $request->getHeaderLine('Content-Type');
        $contentType = $contentType ?: static::DEFAULT_CONTENT_TYPE;
        $contentTypeParts = explode(';', $contentType);
        $mimeType = trim(array_shift($contentTypeParts));
        if (is_null($data)) {
            $body = null;
        } elseif (is_string($data)) {
            $body = $data;
        } elseif (is_array($data)) {
            $body = static::format($data, $mimeType);
        } else {
            throw new InvalidArgumentException('Data should be either null, string or array');
        }
        if ( ! is_null($body)) {
            $contentCharset = $contentCharset ?: static::extractCharset($contentTypeParts);
            $contentCharset = $contentCharset ?: static::DEFAULT_CONTENT_CHARSET;
            $contentTypeReassembled = $mimeType.'; charset='.$contentCharset;
            $request = $request->withHeader('Content-Type', $contentTypeReassembled);

            $request->getBody()->write($body);
        }
        return $request;
    }

    /**
     * Formats $data into string according to mime type specified in $format
     *
     * @param array $data
     * @param string $format mime type
     * @return string
     * @throws \InvalidArgumentException
     */
    protected static function format(array $data, $format)
    {
        $format = strtolower($format);
        switch ($format) {
            case static::CONTENT_TYPE_FORM:
                return http_build_query($data);
            case static::CONTENT_TYPE_JSON:
                return json_encode($data);
            default:
                throw new InvalidArgumentException('Format not supported');
        }
    }

    /**
     * Tries to extract charset from pre-parsed part of Content-Type value
     *
     * @param array $contentTypeParts
     * @return null|string
     */
    private static function extractCharset(array $contentTypeParts)
    {
        foreach ($contentTypeParts as $part) {
            $elements = explode('=', $part);
            $key = trim(array_shift($elements));
            if (strtolower($key) === 'charset') {
                $value = trim(reset($elements));
                return $value ?: null;
            }
        }
        return null;
    }
}
