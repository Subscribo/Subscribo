<?php namespace Subscribo\RestClient\Exceptions;

use Exception;

class TokenConfigurationHttpException extends ConfigurationHttpException {

    const DEFAULT_STATUS_CODE = 571;
    const DEFAULT_EXCEPTION_CODE = 70;
    const DEFAULT_MESSAGE = 'Invalid Configuration: Access token not accepted by remote server';

    public function __construct($responseStatusCode, $responseContent, $statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        $data['originalResponse'] = [
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
