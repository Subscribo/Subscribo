<?php namespace Subscribo\PsrHttpMessageTools\Parsers;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Subscribo\PsrHttpMessageTools\Interfaces\DealingWithContentTypeInterface;

/**
 * Class ResponseParser
 *
 * @package Subscribo\PsrHttpMessageTools
 */
class ResponseParser implements DealingWithContentTypeInterface
{
    /** @var ResponseInterface  */
    protected $response;

    protected $data = false;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    public static function extractDataFromResponse(ResponseInterface $response)
    {
        $instance = new static($response);
        return $instance->extractData();
    }

    /**
     * @return array
     */
    public function extractData()
    {
        if (false !== $this->data) {
            return $this->data;
        }
        $stream = $this->response->getBody();
        if ($stream->tell()) {
            $stream->rewind();
        }
        $body = $stream->getContents();
        $contentType = $this->response->getHeaderLine('Content-Type');
        $contentTypeParts = explode(';', $contentType);
        $mimeType = trim(reset($contentTypeParts));
        $data = static::parseStringByFormat($body, $mimeType);
        $this->data = $data;
        return $data;
    }

    /**
     * @param string $value
     * @param string $format
     * @return array
     * @throws \InvalidArgumentException
     */
    protected static function parseStringByFormat($value, $format)
    {
        if ('' === $value) {
            return [];
        }
        $format = strtolower($format);
        switch ($format) {
            case static::CONTENT_TYPE_FORM:
                $data = [];
                parse_str($value, $data);
                return $data;
            case static::CONTENT_TYPE_JSON:
                $data = json_decode($value, true, 512, JSON_BIGINT_AS_STRING);
                $data = is_array($data) ? $data : [];
                return $data;
            default:
                throw new InvalidArgumentException('Format not supported');
        }
    }

}
