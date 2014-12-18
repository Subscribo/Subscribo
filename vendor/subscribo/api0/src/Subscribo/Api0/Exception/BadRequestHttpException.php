<?php namespace Subscribo\Api0\Exception;

/**
 * Class BadRequestHttpException
 *
 * @package Subscribo\Api0
 */
class BadRequestHttpException extends HttpException {


    public function __construct($message = null, $data = array(), \Exception $previous = null, $headers = array(), $code = 0)
    {
        parent::__construct(400, $message, $data, $previous, $headers, $code);
    }

}
