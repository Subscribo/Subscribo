<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;

class RemoteServerErrorHttpException extends ServerErrorHttpException {

    const DEFAULT_STATUS_CODE = 502;
    const DEFAULT_EXCEPTION_CODE = 80;
    const DEFAULT_MESSAGE = 'Remote Server Error';

    public function __construct(array $data = array(), $statusCode = true, $message = true, $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $statusCode) {
            $statusCode = $this::DEFAULT_STATUS_CODE;
        }
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
