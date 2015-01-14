<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;

class RemoteServerErrorException extends ServerErrorHttpException {

    const DEFAULT_STATUS_CODE = 502;
    const DEFAULT_EXCEPTION_CODE = 80;
    const DEFAULT_MESSAGE = 'Remote Server Error';

    public function __construct($responseStatusCode, $responseContent, $statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        $data['remote'] = [
            'statusCode' => $responseStatusCode,
            'content' => $responseContent,
        ];
        if (true === $code) {
            $code = self::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $statusCode) {
            $statusCode = self::DEFAULT_STATUS_CODE;
        }
        if (true === $message) {
            $message = self::DEFAULT_MESSAGE;
        }

        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }

}
