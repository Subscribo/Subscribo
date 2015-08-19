<?php

namespace Subscribo\TransactionPluginPayUnity\Processors;

use Exception;
use RuntimeException;
use Subscribo\TransactionPluginPayUnity\Drivers\PostDriver;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessorBase;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Utils;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Omnipay\Omnipay;

/**
 * Class CopyAndPayProcessor
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class CopyAndPayProcessor extends TransactionProcessorBase
{
    const OMNIPAY_GATEWAY_NAME = 'PayUnity\\COPYandPAY';

    public function process()
    {
        $transaction = $this->transaction->getTransactionModelInstance();
        if ($transaction->isAutomatic()) {
            $postDriver = $this->driver->getPluginResourceManager()->getDriver(PostDriver::getDriverIdentifier());

            return $postDriver->makeProcessor($this->transaction)->process();
        }
        switch ($transaction->stage) {
            case Transaction::STAGE_PLANNED:

                return $this->processPlannedTransaction();
            case Transaction::STAGE_PREPARED:

                return $this->processPreparedTransaction();
            default:

                throw new RuntimeException('Transaction is in invalid stage for continuing');
        }
    }

    protected function processPlannedTransaction()
    {
        $transaction = $this->transaction;
        $interruption = $this->driver->getPluginResourceManager()->prepareInterruptionFacade($this);
        $configuration = json_decode($transaction->transactionGatewayConfiguration->configuration, true);
        $initializeData = $configuration['initialize'];
        $purchaseData = $configuration['purchase'];
        /** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
        $gateway = Omnipay::create(static::OMNIPAY_GATEWAY_NAME);
        $gateway->initialize($initializeData);
        $purchaseData['amount'] = $transaction->amount;
        $purchaseData['currency'] = $transaction->currency->identifier;
        $purchaseData['transactionId'] = $transaction->hash;
        $purchaseData['returnUrl'] = Utils::assembleWidgetReturnUrl($transaction->service, $interruption->getHash());
        $salesOrder = $transaction->salesOrder;
        if ($salesOrder) {
            $description = Utils::assembleTransactionDescription($salesOrder, $this->getLocalizer());
            $purchaseData['description'] = Utils::limitStringLength($description, 120, ',');
            $purchaseData['card'] = Utils::assembleCardData($salesOrder->billingAddress, $salesOrder->shippingAddress);
        }
        try {
            $purchase = $gateway->purchase($purchaseData);
            $transaction->changeStage(Transaction::STAGE_PREPARATION_REQUESTED);
            $purchaseResponse = $purchase->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_PREPARATION_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);

            throw new ServerErrorHttpException(502, 'Error when trying to contact API', [], 0, $e);
        }
        if ( ! $purchaseResponse->isTransactionToken()) {
            $transaction->changeStage(Transaction::STAGE_PREPARATION_RESPONSE_RECEIVED, Transaction::STATUS_RESPONSE_ERROR, null, 'No token');

            throw new ServerErrorHttpException(502, 'Wrong response from API contacted');
        }
        $transaction->setDataToRemember($purchaseResponse->getTransactionToken(), 'transactionToken');
        $transaction->changeStage(Transaction::STAGE_PREPARED, Transaction::STATUS_WIDGET_PROVIDED, ['receive']);
        $widget = $purchaseResponse->getWidget();

        return $this->driver->getPluginResourceManager()->interruptByWidget($widget, $this, $interruption);
    }

    protected function processPreparedTransaction()
    {
        $transaction = $this->transaction;
        $transactionToken =  $transaction->getDataToRemember('transactionToken');
        $answer = $transaction->getAnswerFromWidget();
        if (empty($answer['request']['query']['token'])) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_RESPONSE_RECEIVED, Transaction::STATUS_RESPONSE_ERROR);

            throw new ServerErrorHttpException(502, 'Wrong response from API contacted');
        }
        if (strval($transactionToken) !== strval($answer['request']['query']['token'])) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_RESPONSE_RECEIVED, Transaction::STATUS_RESPONSE_ERROR);

            throw new ServerErrorHttpException(500, 'Token does not match with the one in internal system');
        }
        $configuration = json_decode($transaction->transactionGatewayConfiguration->configuration, true);
        $initializeData = $configuration['initialize'];
        /** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
        $gateway = Omnipay::create(static::OMNIPAY_GATEWAY_NAME);
        $gateway->initialize($initializeData);
        try {
            $completePurchase = $gateway->completePurchase();
            $completePurchase->setTransactionToken($transactionToken);
            $transaction->changeStage(Transaction::STAGE_CHARGE_COMPLETING_REQUESTED);
            $completePurchaseResponse = $completePurchase->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_COMPLETING_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);

            throw new ServerErrorHttpException(502, 'Error when trying to contact API', [], 0, $e);
        }
        $transaction->message = $completePurchaseResponse->getMessage();
        $transaction->code = $completePurchaseResponse->getCode();
        $transaction->reference = $completePurchaseResponse->getTransactionReference();
        $message = null;
        if ($completePurchaseResponse->isSuccessful()) {
            $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_ACCEPTED, ['receive', 'finalize']);
            $status = TransactionProcessingResultInterface::STATUS_SUCCESS;

        } else {
            $transaction->changeStage(Transaction::STAGE_FAILED, Transaction::STATUS_FAILED, ['receive']);
            $status = TransactionProcessingResultInterface::STATUS_FAILURE;
            $message = TransactionProcessingResultBase::makeGenericMessage($status, $this->getLocalizer());
        }
        $registrationToken = $completePurchaseResponse->getAccountRegistration();
        $registered = Utils::rememberRegistrationToken($transaction, $registrationToken) ? true : false;

        return new TransactionProcessingResultBase($transaction, $status, $message, $registered);
    }
}
