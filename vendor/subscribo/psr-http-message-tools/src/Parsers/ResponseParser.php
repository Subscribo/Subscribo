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
     * Parses data of Content-Type application/x-www-form-urlencoded into an array
     * @param string $data
     * @return array
     */
    public static function parseContentTypeApplicationXWwwFormUrlencoded($data)
    {
        $result = [];
        $parts = explode('&', $data);
        foreach ($parts as $part) {
            $elements = explode('=', $part, 2);
            $key = urldecode($elements[0]);
            if (empty($key)) {
                continue;
            }
            $value = isset($elements[1]) ? urldecode($elements[1]) : null;
            if (isset($result[$key])) {
                $result[$key] = (array) ($result[$key]);
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Manually process that kind of transformation of one-dimensional array into multidimensional
     * which PHP's parse_str() does
     *
     * @param array $source
     * @return array
     */
    public static function transformBracketedArrayIntoMultidimensional(array $source)
    {
        $result = [];
        foreach ($source as $key => $value) {
            $keyParts = explode('[', $key);
            $keyPartsReversed = array_reverse($keyParts);
            $toAdd = $value;
            foreach ($keyPartsReversed as $keyPart) {
                $keyCleared = trim($keyPart, '[]');
                if ($keyCleared) {
                    $toAdd = [$keyCleared => $toAdd];
                }
            }
            $toAdd = (array) $toAdd;
            $result = array_replace_recursive($result, $toAdd);
        }
        return $result;
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
                return static::parseContentTypeApplicationXWwwFormUrlencoded($value);
            case static::CONTENT_TYPE_JSON:
                $data = json_decode($value, true, 512, JSON_BIGINT_AS_STRING);
                $data = is_array($data) ? $data : [];
                return $data;
            default:
                throw new InvalidArgumentException('Format not supported');
        }
    }

}
