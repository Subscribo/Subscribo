<?php

namespace Subscribo\ModelCore\Exceptions;

use Exception;

class ArgumentValidationException extends Exception
{
    const DEFAULT_MESSAGE = 'Invalid argument';
    const DEFAULT_CODE = 0;

    public $type;
    public $key;
    public $data = [];

    public function __construct($type = null, $key = null, $data = [], $message = true, $code = true, Exception $previous = null)
    {
        if (true === $message) {
            $message = static::DEFAULT_MESSAGE;
        }
        if (true === $code) {
            $code = static::DEFAULT_CODE;
        }
        $this->type = $type;
        $this->key = $key;
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getData()
    {
        return $this->data;
    }
}
