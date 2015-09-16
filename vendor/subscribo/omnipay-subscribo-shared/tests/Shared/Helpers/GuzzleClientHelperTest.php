<?php

namespace Subscribo\Omnipay\Shared\Helpers;

use PHPUnit_Framework_TestCase;
use Guzzle\Http\Client;
use Subscribo\Omnipay\Shared\Helpers\GuzzleClientHelper;

class GuzzleClientHelperTest extends PHPUnit_Framework_TestCase
{
    public function testAddPsrLoggerToClient()
    {
        $client = new Client();
        $listenersBefore = $client->getEventDispatcher()->getListeners('request.sent');
        $logger = $this->getMockForAbstractClass('Psr\\Log\\LoggerInterface');
        $plugin = GuzzleClientHelper::addPsrLoggerToClient($client, $logger);
        $this->assertTrue($client->getEventDispatcher()->hasListeners('request.sent'));
        $expectedListenersCount = (count($listenersBefore) + 1);
        $this->assertCount($expectedListenersCount, $client->getEventDispatcher()->getListeners('request.sent'));
        $this->assertContains([$plugin, 'onRequestSent'], $client->getEventDispatcher()->getListeners('request.sent'));
    }
}
