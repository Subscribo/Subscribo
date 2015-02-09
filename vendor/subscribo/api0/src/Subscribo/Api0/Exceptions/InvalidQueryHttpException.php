<?php namespace Subscribo\Api0\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\BadRequestHttpException;

/**
 * Class InvalidQueryHttpException
 *
 * Class for Url query string or parameters errors
 *
 * @package Subscribo\Exception\Exceptions
 */
class InvalidQueryHttpException extends BadRequestHttpException {

    const DEFAULT_EXCEPTION_CODE = 20;
    const DEFAULT_MESSAGE = 'Invalid query parameters';

    public function __construct($message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = self::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = self::DEFAULT_MESSAGE;
        }

        parent::__construct($message, $data, $code, $previous, $headers);
    }

}