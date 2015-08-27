<?php

namespace Subscribo\TransactionPluginPayUnity\Processors;

use Exception;
use RuntimeException;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessorBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Omnipay\Omnipay;
use Subscribo\ModelCore\Models\Transaction;

/**
 * Class PostProcessor
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class PostProcessor extends TransactionProcessorBase
{
    const OMNIPAY_GATEWAY_NAME = 'PayUnity\\Post';

    /**
     * @return TransactionProcessingResultInterface
     * @throws \RuntimeException
     */
    public function doProcess()
    {
        $this->checkInitialStage();
        $this->switchResultMoneyStart();
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
            $this->switchResultMoneyTransferred(false);
            $purchaseResponse = $purchaseRequest->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);
            $this->getLogger()->error($e);

            return $this->result->error(TransactionProcessingResultInterface::ERROR_CONNECTION);
        }
        $transaction->reference = $purchaseResponse->getTransactionReference();
        $transaction->message = $purchaseResponse->getMessage();
        $transaction->code = $purchaseResponse->getCode();
        if ($purchaseResponse->isSuccessful()) {
            $this->switchResultMoneyTransferred(true);
            $status = TransactionProcessingResultInterface::STATUS_SUCCESS;
            $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_ACCEPTED, ['receive', 'finalize']);
        } else {
            $this->switchResultMoneyStart();
            $status = TransactionProcessingResultInterface::STATUS_FAILURE;
            $transaction->changeStage(Transaction::STAGE_FAILED, Transaction::STATUS_FAILED, ['receive']);
        }

        return $this->result->setStatus($status);
    }
}
