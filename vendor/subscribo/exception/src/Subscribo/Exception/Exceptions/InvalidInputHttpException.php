<?php namespace Subscribo\Exception\Exceptions;

use Exception;

/**
 * Class InvalidInputHttpException
 *
 * Class for Input Validation errors
 *
 * @package Subscribo\Exception
 */
class InvalidInputHttpException extends BadRequestHttpException {

    const DEFAULT_EXCEPTION_CODE = 10;
    const DEFAULT_MESSAGE = 'Invalid input';

    public function __construct(array $validationErrors = null, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = self::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = self::DEFAULT_MESSAGE;
        }
        if ( ! is_null($validationErrors)) {
            $data['output']['validationErrors'] = $validationErrors;
        }
        parent::__construct($message, $data, $code, $previous, $headers);
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        $data = $this->getData();
        if (empty($data['output']['validationErrors'])) {
            return array();
        }
        return $data['output']['validationErrors'];
    }
}
