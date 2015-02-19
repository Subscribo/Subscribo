<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Traits\ContainDataTrait;
use Subscribo\Exception\Interfaces\ContainDataInterface;

/**
 * Class HttpException
 *
 * Base class for Http Exceptions containing data
 *
 * @package Subscribo\Exception
 */
class HttpException extends \Symfony\Component\HttpKernel\Exception\HttpException implements ContainDataInterface {

    use ContainDataTrait;

    /**
     * @param int|string $statusCode
     * @param string|null $message
     * @param array $data important key is 'error' - this part is about to be rendered
     * @param int $code
     * @param Exception|null $previous
     * @param array $headers
     */
    public function __construct($statusCode, $message = null, array $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        $this->_containedData = $data;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

}
