<?php namespace Subscribo\Omnipay\Shared;

use Psr\Log\LoggerInterface;
use Omnipay\Common\AbstractGateway as Base;
use Subscribo\Omnipay\Shared\Helpers\GuzzleClientHelper;

/**
 * Class AbstractGateway
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractGateway extends Base
{
    /** @var  LoggerInterface|null */
    protected $psrLogger;

    /**
     * @param LoggerInterface $logger
     * @param null|bool|string|\Guzzle\Log\MessageFormatter $formatter
     * @return bool|null
     */
    public function attachPsrLogger(LoggerInterface $logger, $formatter = null)
    {
        if ($this->psrLogger) {
            return null;
        }
        GuzzleClientHelper::addPsrLoggerToClient($this->httpClient, $logger, $formatter);
        $this->psrLogger = $logger;
        return true;
    }

}
