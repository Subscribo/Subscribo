<?php

namespace Omnipay\PayUnity;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\COPYandPAYGateway;
use Guzzle\Log\MessageFormatter;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;


class CopyAndPayGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new COPYandPAYGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->logger = new \Monolog\Logger('UnitTest logger');
        $this->logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../tmp/logs/unit-tests.log'));
        $this->gateway->initializeLogger($this->logger);

        $this->gateway->setSecuritySender('ff80808144d46be50144d4a6f6ce007f');
        $this->gateway->setTransactionChannel('ff80808144d46be50144d4a732ae0083');
        $this->gateway->setUserLogin('ff80808144d46be50144d4a6f6cf0081');
        $this->gateway->setUserPwd('M5Ynx692');
        $this->gateway->setIdentificationShopperId('Shopper 13245');
        $this->options = array(
            'amount' => '10.00',
            'currency' => 'EUR',
        );
        $this->connectorModeGateway = new COPYandPAYGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->connectorModeGateway->initialize([
            "securitySender" => "696a8f0fabffea91517d0eb0a0bf9c33",
            "transactionChannel" => "52275ebaf361f20a76b038ba4c806991",
            "transactionMode" => "CONNECTOR_TEST",
            "userLogin" => "1143238d620a572a726fe92eede0d1ab",
            "userPwd" => "demo",
            "testMode" => true,
            'identificationBulkId' => 'Some bulk ID'
        ]);
    }

    /**
     * @return CopyAndPayPurchaseResponse
     */
    public function testPurchase()
    {
        $options = $this->options;
        $options['returnUrl'] = 'https://localhost/redirect/url';
        $options['brands'] = 'VISA';
        $options['identificationTransactionId'] = 'Transaction 12345';
        $request = $this->gateway->purchase($options);
        $request->setPresentationUsage('Used for test');
        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseRequest', $request);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var \Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest $request */
        $this->assertSame('Shopper 13245', $request->getIdentificationShopperId());
        $this->assertSame('Transaction 12345', $request->getIdentificationTransactionId());
        $this->assertSame('Used for test', $request->getPresentationUsage());
        $this->assertEmpty($request->getIdentificationBulkId());
        $this->assertEmpty($request->getIdentificationInvoiceId());
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $widget = $response->getWidget();
        $this->assertNotEmpty($widget);
        $this->assertStringEndsWith('>VISA</form>', $widget);
        $this->assertStringEndsWith('>VISA</form>', $response->getWidgetForm());
        $this->assertStringStartsWith('<form action="https://localhost/redirect/url"', $response->getWidgetForm());
        $this->assertNotEmpty($response->getWidget());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        return $response;
    }

    /**
     * @depends testPurchase
     * @param CopyAndPayPurchaseResponse $purchaseResponse
     */
    public function testWaitingCompletePurchase(CopyAndPayPurchaseResponse $purchaseResponse)
    {
        $response = $this->gateway->completePurchase()->fill($purchaseResponse)->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseResponse', $response);
        /** @var CopyAndPayCompletePurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertTrue($response->isWaiting());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertEmpty($response->getIdentificationTransactionId());
        $this->assertEmpty($response->getIdentificationShopperId());
        $this->assertEmpty($response->getIdentificationUniqueId());
        $this->assertEmpty($response->getIdentificationShortId());
    }

    public function testConnectorModePurchase()
    {
        $options = $this->options;
        $options['brands'] = ['MAESTRO', 'MASTER'];
        $options['paymentMemo'] = 'TEST MEMO';

        $request = $this->connectorModeGateway->purchase($options);
        $request->setIdentificationInvoiceId(248);
        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseRequest', $request);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var \Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest $request */
        $this->assertEmpty($request->getIdentificationShopperId());
        $this->assertEmpty($request->getIdentificationTransactionId());
        $this->assertSame('Some bulk ID', $request->getIdentificationBulkId());
        $this->assertSame(248, $request->getIdentificationInvoiceId());
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $widget = $response->getWidget(null, null, false, null, '/redirect/url');
        $this->assertNotEmpty($widget);
        $this->assertStringEndsWith('>MAESTRO MASTER</form>', $widget);
        $this->assertStringEndsWith('>MAESTRO MASTER</form>', $response->getWidgetForm(null, 'https://localhost/redirect/url'));
        $this->assertStringStartsWith('<form action="https://localhost/redirect/url"', $response->getWidgetForm(null, 'https://localhost/redirect/url'));
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());

        return $response;
    }


    /**
     * @depends testConnectorModePurchase
     * @param CopyAndPayPurchaseResponse $purchaseResponse
     */
    public function testConnectorModeWaitingCompletePurchase(CopyAndPayPurchaseResponse $purchaseResponse)
    {
        $response = $this->connectorModeGateway->completePurchase()->fill($purchaseResponse)->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseResponse', $response);
        /** @var CopyAndPayCompletePurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertTrue($response->isWaiting());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertEmpty($response->getIdentificationTransactionId());
        $this->assertEmpty($response->getIdentificationShopperId());
        $this->assertEmpty($response->getIdentificationUniqueId());
        $this->assertEmpty($response->getIdentificationShortId());
    }


    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage brands
     */
    public function testEmptyBrandsPurchase()
    {
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getWidget());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage returnUrl
     */
    public function testEmptyReturnUrlPurchase()
    {
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getWidget(null, null, false, 'VISA'));
    }

    public function testInvalidTokenCompletePurchase()
    {
        $response = $this->gateway->completePurchase()->setTransactionToken('TEST_INVALID_TOKEN')->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseResponse', $response);
        /** @var CopyAndPayCompletePurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertSame('Invalid or expired token', $response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
    }
}
