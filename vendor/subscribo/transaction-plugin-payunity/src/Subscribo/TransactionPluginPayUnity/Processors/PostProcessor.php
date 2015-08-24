<?php

namespace Subscribo\TransactionPluginPayUnity\Processors;

use Exception;
use RuntimeException;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessorBase;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Omnipay\Omnipay;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\ModelCore\Models\Transaction;


/**
 * Class PostProcessor
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class PostProcessor extends TransactionProcessorBase
{
    const OMNIPAY_GATEWAY_NAME = 'PayUnity\\Post';

    public function process()
    {
        $transaction = $this->transaction;
        $registrationToken = $transaction->retrieveRegistrationToken();
        if (empty($registrationToken)) {

            throw new RuntimeException('This driver is not able to process transactions without registration token');
        }
        /** @var \Omnipay\PayUnity\PostGateway $gateway */
        $gateway = Omnipay::create(static::OMNIPAY_GATEWAY_NAME);
        $gateway->initialize($transaction->getGatewayConfiguration('initialize'));
        $purchaseRequest = $gateway->purchase();
        $purchaseRequest->setAmount($transaction->amount);
        $purchaseRequest->setCurrency($transaction->currency->identifier);
        $purchaseRequest->setCardReference($registrationToken);
        $purchaseRequest->setTransactionId($transaction->hash);
        try {
            $transaction->changeStage(Transaction::STAGE_CHARGE_REQUESTED);
            $purchaseResponse = $purchaseRequest->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);
            $this->driver->getPluginResourceManager()->getLogger()->debug($e);
            throw new ServerErrorHttpException(502, 'Error when communicating with API');
        }
        $transaction->reference = $purchaseResponse->getTransactionReference();
        $transaction->message = $purchaseResponse->getMessage();
        $transaction->code = $purchaseResponse->getCode();
        $message = null;
        if ($purchaseResponse->isSuccessful()) {
            $status = TransactionProcessingResultInterface::STATUS_SUCCESS;
            $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_ACCEPTED, ['receive', 'finalize']);
        } else {
            $status = TransactionProcessingResultInterface::STATUS_FAILURE;
            $message = TransactionProcessingResultBase::makeGenericMessage($status, $this->getLocalizer());
            $transaction->changeStage(Transaction::STAGE_FAILED, Transaction::STATUS_FAILED, ['receive']);
        }

        return new TransactionProcessingResultBase($transaction, $status, $message);
    }

}
