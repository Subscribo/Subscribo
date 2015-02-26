<?php namespace Subscribo\RestCommon;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Subscribo\RestCommon\Interfaces\ServerRequestInterface;
use Subscribo\RestCommon\Exceptions\InvalidArgumentException;


class ServerRequest implements ServerRequestInterface, JsonSerializable, Arrayable
{
    protected $data = array();

    const TYPE = 'request';

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
        $this->data = $data;
        return $this;
    }

    public function export()
    {
        return $this->data;
    }

    public function getType()
    {
        return $this::TYPE;
    }

    public function jsonSerialize()
    {
        return $this->export();
    }

    public function toArray()
    {
        return $this->export();
    }
}
