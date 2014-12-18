<?php namespace Subscribo\Api0\Exception;

/**
 * Class NotFoundHttpException
 *
 * @package Subscribo\Api0
 */
class NotFoundHttpException extends HttpException {


    public function __construct($message = null, $data = array(), \Exception $previous = null, $headers = array(), $code = 0)
    {
        parent::__construct(404, $message, $data, $previous, $headers, $code);
    }

}
