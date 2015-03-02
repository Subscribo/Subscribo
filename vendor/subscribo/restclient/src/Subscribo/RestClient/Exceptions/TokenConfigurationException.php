<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\ConfigurationException;

/**
 * Class TokenConfigurationException
 *
 * @package Subscribo\RestClient
 */
class TokenConfigurationException extends ConfigurationException {

    const DEFAULT_EXCEPTION_CODE = 70;

    const DEFAULT_MESSAGE = 'Invalid Configuration: Access token not accepted by remote server';

    /**
     * @param int|string $responseStatusCode
     * @param string $responseContent
     * @param string|bool $message
     * @param array $data
     * @param int|bool $code
     * @param null|Exception $previous
     */
    public function __construct($responseStatusCode, $responseContent, $message = true, array $data = array(), $code = true, Exception $previous = null)
    {
        $data['originalResponse'] = [
            'statusCode' => $responseStatusCode,
            'content' => $responseContent,
        ];
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        parent::__construct($data, $message, $code, $previous);
    }
}
