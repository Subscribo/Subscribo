<?php namespace Subscribo\Exception\Exceptions;


use Exception;

/**
 * Class InstanceNotFoundHttpException
 *
 * Http Exception class for cases, when the uri is generally valid, but particular model instance is not available
 *
 * @package Subscribo\Exception
 */
class InstanceNotFoundHttpException extends NotFoundHttpException {

    const DEFAULT_EXCEPTION_CODE = 50;

    public function __construct($instanceName = true, $message = true, $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = self::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $instanceName) {
            $instanceName = 'Requested instance';
        }
        if (true === $message) {
            $message = $instanceName.' not found';
        }
        parent::__construct($message, $data, $code, $previous, $headers);
    }
}