<?php namespace Subscribo\Exception\Traits;

/**
 * Trait StatusMessageTrait
 *
 * Trait helping to implement specific (non inherited) part of \Subscribo\Exception\Interfaces\HttpExceptionInterface
 *
 * @package Subscribo\Exception
 */
trait StatusMessageTrait {

    /**
     * @var string
     */
    protected $_statusMessage;

    /**
     * @param string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->_statusMessage = $statusMessage;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }
}
