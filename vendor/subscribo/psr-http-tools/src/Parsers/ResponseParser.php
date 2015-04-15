<?php namespace Subscribo\PsrHttpTools\Parsers;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Subscribo\PsrHttpTools\Interfaces\DealingWithContentTypeInterface;

class ResponseParser implements DealingWithContentTypeInterface
{
    /** @var ResponseInterface  */
    protected $response;

    protected $data = false;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public static function extractDataFromResponse(ResponseInterface $response)
    {
        $instance = new static($response);
        return $instance->extractData();
    }

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

    protected static function parseStringByFormat($value, $format)
    {
        $format = strtolower($format);
        switch ($format) {
            case static::CONTENT_TYPE_FORM:
                $data = [];
                parse_str($value, $data);
                return $data;
            case static::CONTENT_TYPE_JSON:
                return json_decode($value, true, 512, JSON_BIGINT_AS_STRING);
            default:
                throw new InvalidArgumentException('Format not supported');
        }
    }

}
