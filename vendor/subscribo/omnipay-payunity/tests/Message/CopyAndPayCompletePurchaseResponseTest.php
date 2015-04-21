<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;

class CopyAndPayCompletePurchaseResponseTest extends TestCase
{

    public function testWaiting()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                'token' => 'D480CB27803A2115D52A03AE9239042C.sbg-vm-fe01',
                'transaction' => [
                    'processing' => [
                        'result' => 'WAITING FOR SHOPPER',
                    ],
                ],
            ]
        );
        $this->assertTrue($response->isWaiting());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getTransactionReference());
    }

    public function testSuccess()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                "transaction" => [
                    "channel" => "c1c021a4bfca258d4da22a655dc42966",
                    "identification" => [
                        "shopperid" => "admin",
                        "shortId" => "7307.0292.8546",
                        "transactionid" => "20130129120736562fb049d9e1aee0686f9005f4515f2e",
                        "uniqueId" => "40288b163c865d30013c86600d6d0002"
                    ],
                    "mode" => "CONNECTOR_TEST",
                    "payment" => [
                        "code" => "CC.DB"
                    ],
                    "processing" => [
                        "code" => "CC.DB.90.00",
                        "reason" => [
                            "code" => "00",
                            "message" => "Successful Processing"
                        ],
                        "result" => "ACK",
                        "return" => [
                            "code" => "000.100.112",
                            "message" => "Request successfully processed in Merchant in Connector Test Mode"
                        ],
                        "timestamp" => "2013-01-29 12 => 55 => 14"
                    ],
                    "response" => "SYNC"
                ]
            ]
        );
        $this->assertFalse($response->isWaiting());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertNotEmpty($response->getCode());
        $this->assertSame('000.100.112', $response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame('Request successfully processed in Merchant in Connector Test Mode', $response->getMessage());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('40288b163c865d30013c86600d6d0002', $response->getTransactionReference());
    }

    public function testRejected()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                "transaction" => [
                    "channel" => "c1c021a4bfca258d4da22a655dc42966",
                    "identification" => [
                        "shopperid" => "admin",
                        "shortId" => "0435.0816.1186",
                        "transactionid" => "20130129120736562fb049d9e1aee0686f9005f4515f2e",
                        "uniqueId" => "40288b163c865d30013c866d69a2002a"
                    ],
                    "mode" => "CONNECTOR_TEST",
                    "payment" => [
                        "code" => "CC.DB"
                    ],
                    "processing" => [
                        "code" => "CC.DB.70.40",
                        "reason" => [
                            "code" => "40",
                            "message" => "Account Validation"
                        ],
                        "result" => "NOK",
                        "return" => [
                            "code" => "100.100.700",
                            "message" => "invalid cc number/brand combination"
                        ],
                        "timestamp" => "2013-01-29 13 => 09 => 42"
                    ],
                    "response" => "SYNC"
                ]
            ]
        );
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertNotEmpty($response->getCode());
        $this->assertSame('100.100.700', $response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame('invalid cc number/brand combination', $response->getMessage());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('40288b163c865d30013c866d69a2002a', $response->getTransactionReference());
    }

    public function testInvalidResponse()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            ["errorMessage" => "Invalid or expired token",]
        );
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertEmpty($response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame('Invalid or expired token', $response->getMessage());
        $this->assertEmpty($response->getTransactionReference());
    }

}
