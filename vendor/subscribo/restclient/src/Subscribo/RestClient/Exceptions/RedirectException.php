<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\ResponseException;

/**
 * Class RedirectException
 *
 * Base class for converting 5xx responses to exceptions
 *
 * @package Subscribo\RestClient
 */
class RedirectException extends ResponseException
{
    const STATUS_CODE = 300;
    const DEFAULT_MESSAGE = 'Redirect';

    public function __construct($statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $statusCode) {
            $statusCode = $this::STATUS_CODE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
