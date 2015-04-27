<?php

namespace Subscribo\Omnipay\Shared\Helpers;

use Psr\Log\LoggerInterface;
use Guzzle\Http\ClientInterface;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Log\MessageFormatter;


/**
 * Class GuzzleClientHelper
 *
 * This helper can be used to help adding PSR Compliant logger to guzzle client
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class GuzzleClientHelper
{
    const SIMPLE_FORMAT = "Guzzle request sent to {host} : \n{request}\n>>>>>\n{response}\n-----\n";

    /**
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     * @param null|string $formatter
     * @return LogPlugin
     */
    public static function addPsrLoggerToClient(ClientInterface $client, LoggerInterface $logger, $formatter = self::SIMPLE_FORMAT)
    {
        $formatter = is_null($formatter) ? self::SIMPLE_FORMAT : $formatter;
        $adapter = new PsrLogAdapter($logger);
        $plugin = new LogPlugin($adapter, $formatter);
        $client->addSubscriber($plugin);
        return $plugin;
    }

}
