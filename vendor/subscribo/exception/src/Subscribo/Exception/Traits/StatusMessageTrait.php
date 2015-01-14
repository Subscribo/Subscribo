<?php namespace Subscribo\Exception\Traits;

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
