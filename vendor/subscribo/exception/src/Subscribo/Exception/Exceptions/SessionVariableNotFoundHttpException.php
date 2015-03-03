<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ClientErrorHttpException;

class SessionVariableNotFoundHttpException extends ClientErrorHttpException
{
    const STATUS_CODE = 400;
    const DEFAULT_EXCEPTION_CODE = 0;
    const DEFAULT_MESSAGE = 'Session lost';

    public function __construct($message = true, array $data = null, $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $message) {
            $message = static::DEFAULT_MESSAGE;
        }
        if (true === $code) {
            $code = static::DEFAULT_EXCEPTION_CODE;
        }
        if (is_null($data)) {
            $data = [];
        }
        parent::__construct(static::STATUS_CODE, $message, $data, $code, $previous, $headers);

    }
}
