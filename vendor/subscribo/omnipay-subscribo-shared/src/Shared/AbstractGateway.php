<?php namespace Subscribo\Omnipay\Shared;

use Psr\Log\LoggerInterface;
use Guzzle\Log\MessageFormatter;
use Omnipay\Common\AbstractGateway as Base;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Subscribo\Omnipay\Shared\Traits\PsrLoggerAddingTrait;

/**
 * Class AbstractGateway
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractGateway extends Base
{
    use PsrLoggerAddingTrait;

    protected $psrLoggerAdded = false;

    /**
     * @param LoggerInterface $logger
     * @param null|bool|string|MessageFormatter $formatter
     */
    public function initializeLogger(LoggerInterface $logger, $formatter = true)
    {
        if ($this->psrLoggerAdded) {
            return;
        }
        $this->addPsrLogger($logger, $formatter);
        $this->psrLoggerAdded = true;
    }

}
