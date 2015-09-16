<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ForbiddenHttpException;

/**
 * Class NoAccountHttpException
 *
 * @package Subscribo\Exception
 */
class NoAccountHttpException extends ForbiddenHttpException
{
    const DEFAULT_EXCEPTION_CODE = 57;
    const DEFAULT_MESSAGE = 'Customer is not logged in';

    public function __construct($message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        parent::__construct($message, $data, $code, $previous, $headers);
    }
}
