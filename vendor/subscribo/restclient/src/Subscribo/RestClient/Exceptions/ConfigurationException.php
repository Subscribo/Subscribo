<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use RuntimeException;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Traits\ContainDataTrait;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class ConfigurationException
 *
 * @package Subscribo\RestClient
 */
class ConfigurationException extends RuntimeException implements ContainDataInterface, MarkableExceptionInterface
{
    use ContainDataTrait
    use MarkableExceptionTrait

    protected $_containedData = array();

    public function __construct(array $data = array(), $message = '', $code = 0, Exception $previous)
    {
        $this->_containedData = $data;
        parent::__construct($message, $code, $previous);
    }
}
