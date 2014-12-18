<?php namespace Subscribo\Api0\Exception;

/**
 * Class InternalServerErrorHttpException
 *
 * @package Subscribo\Api0
 */
class InternalServerErrorHttpException extends HttpException {


    public function __construct($message = null, $data = array(), \Exception $previous = null, $headers = array(), $code = 0)
    {
        parent::__construct(500, $message, $data, $previous, $headers, $code);
    }

}
