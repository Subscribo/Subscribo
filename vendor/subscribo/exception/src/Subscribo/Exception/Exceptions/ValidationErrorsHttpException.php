<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Interfaces\ValidationErrorsInterface;
use Subscribo\Exception\Traits\ValidationErrorsTrait;

/**
 * Class ValidationErrorsHttpException
 *
 * Base class for exceptions containing validation errors
 *
 * @package Subscribo\Exception\Exceptions
 */
class ValidationErrorsHttpException extends BadRequestHttpException implements ValidationErrorsInterface
{
    use ValidationErrorsTrait;

    const DEFAULT_EXCEPTION_CODE = 0;
    const DEFAULT_MESSAGE = 'Validation error';

    public function __construct(array $validationErrors = null, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        if ( ! is_null($validationErrors)) {
            $data[$this->getKey()]['validationErrors'] = $validationErrors;
        }
        parent::__construct($message, $data, $code, $previous, $headers);
    }

}
