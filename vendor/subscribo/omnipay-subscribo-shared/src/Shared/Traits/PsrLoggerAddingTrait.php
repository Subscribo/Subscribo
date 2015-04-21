<?php namespace Subscribo\Omnipay\Shared\Traits;

use Psr\Log\LoggerInterface;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Log\MessageFormatter;


/**
 * Trait PsrLogAddingTrait
 *
 * Note: This trait can be used to help adding PSR Compliant logger to guzzle client, it does not add the logger itself
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
trait PsrLoggerAddingTrait
{
    /**
     * @param LoggerInterface $logger
     * @param MessageFormatter|string $formatter
     */
    protected function addPsrLogger(LoggerInterface $logger, $formatter = MessageFormatter::DEFAULT_FORMAT)
    {
        if (true === $formatter) {
            $formatter = $this->getTestMode() ? MessageFormatter::DEBUG_FORMAT : MessageFormatter::SHORT_FORMAT;
        } elseif (empty($formatter)) {
            $formatter = MessageFormatter::DEFAULT_FORMAT;
        }
        $adapter = new PsrLogAdapter($logger);
        $plugin = new LogPlugin($adapter, $formatter);
        $this->httpClient->addSubscriber($plugin);
    }

}
