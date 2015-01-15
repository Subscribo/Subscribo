<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use GuzzleHttp\Exception\ConnectException;

class ConnectionToRemoteServerHttpException extends ServerErrorHttpException {

    const DEFAULT_STATUS_CODE = 502;
    const DEFAULT_EXCEPTION_CODE = 90;
    const DEFAULT_MESSAGE = 'Could not connect to remote server';

    public function __construct(Exception $previous = null, $statusCode = true, $message = true, array $data = array(), $code = true, array $headers = array())
    {
        if (true === $statusCode) {
            $statusCode = self::DEFAULT_STATUS_CODE;
        }
        if (true === $code) {
            $code = self::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = self::DEFAULT_MESSAGE;
        }
        if ($previous instanceof ConnectException) {
            $data['connection']['error']['message'] = $previous->getMessage();
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }

}
