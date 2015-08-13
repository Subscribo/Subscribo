<?php

namespace Subscribo\RestCommon;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Subscribo\RestCommon\Interfaces\ServerRequestInterface;
use Subscribo\RestCommon\Exceptions\InvalidArgumentException;

/**
 * Class ServerRequest
 *
 * @package Subscribo\RestCommon
 */
class ServerRequest implements ServerRequestInterface, JsonSerializable, Arrayable
{
    const TYPE = 'request';

    /** @var int  */
    public $code = 0;

    /** @var string */
    public $hash;

    /** @var string */
    public $endpoint;

    /** @var string|null  */
    public $locale;

    /** @var  string|null */
    public $domain;

    public function __construct(array $data = array())
    {
        if (is_array($data)) {
            $this->import($data);
        } else {
            throw new InvalidArgumentException('ServerRequest constructor can accept only array as an argument');
        }
    }

    public function import(array $data)
    {
        if ( ! empty($data['hash'])) {
            $this->hash = $data['hash'];
        }
        if ( ! empty($data['endpoint'])) {
            $this->endpoint = $data['endpoint'];
        }
        if ( array_key_exists('code', $data)) {
            $this->code = $data['code'];
        }
        if (isset($data['locale'])) {
            $this->locale = $data['locale'];
        }
        if ( ! empty($data['domain'])) {
            $this->domain = $data['domain'];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function export()
    {
        $result = array();
        $result['type'] = $this->getType();
        $result['code'] = $this->code;
        $result['hash'] = $this->hash;
        $result['endpoint'] = $this->endpoint;
        $result['locale']   = $this->locale;
        $result['domain'] = $this->domain;

        return $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this::TYPE;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->export();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->export();
    }
}
