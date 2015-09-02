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

    /**
     * @return TransactionProcessingResultBase|TransactionProcessingResultInterface
     * @throws \RuntimeException
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     * @throws \Subscribo\Exception\Exceptions\ServerErrorHttpException
     */
    protected function doProcess()
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

    /**
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     * @throws \Subscribo\Exception\Exceptions\ServerErrorHttpException
     */
    protected function processPlannedTransaction()
    {
        $this->switchResultMoneyStart();
        $transaction = $this->transaction;
        $interruption = $this->driver->getPluginResourceManager()->prepareInterruptionFacade($this);
        $configuration = $transaction->getGatewayConfiguration();
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
            $this->getLogger()->error($e);

            return $this->result->error(TransactionProcessingResultInterface::ERROR_CONNECTION);
        }
        if ( ! $purchaseResponse->isTransactionToken()) {
            $transaction->changeStage(Transaction::STAGE_PREPARATION_RESPONSE_RECEIVED, Transaction::STATUS_RESPONSE_ERROR, null, 'No token');
            $this->getLogger()->error('No transaction token found in API response');

            return $this->result->error(TransactionProcessingResultInterface::ERROR_RESPONSE);
        }
        $transaction->setDataToRemember($purchaseResponse->getTransactionToken(), 'transactionToken');
        $transaction->changeStage(Transaction::STAGE_PREPARED, Transaction::STATUS_WIDGET_PROVIDED, ['receive']);
        $widget = $purchaseResponse->getWidget();

        return $this->driver->getPluginResourceManager()->interruptByWidget($widget, $this, $interruption);
    }

    /**
     * @return TransactionProcessingResultBase
     * @throws \Subscribo\Exception\Exceptions\ServerErrorHttpException
     */
    protected function processPreparedTransaction()
    {
        $this->switchResultMoneyTransferred(false);
        $transaction = $this->transaction;
        $transactionToken =  $transaction->getDataToRemember('transactionToken');
        $answer = $transaction->getAnswerFromWidget();
        if (empty($answer['request']['query']['token'])) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_RESPONSE_RECEIVED, Transaction::STATUS_RESPONSE_ERROR);
            $this->getLogger()->error('No transaction token found in query');

            return $this->result->error(TransactionProcessingResultInterface::ERROR_RESPONSE);
        }
        if (strval($transactionToken) !== strval($answer['request']['query']['token'])) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_RESPONSE_RECEIVED, Transaction::STATUS_RESPONSE_ERROR);
            $this->getLogger()->error('Transaction token does not match with the one in internal system');

            return $this->result->error(TransactionProcessingResultInterface::ERROR_RESPONSE);
        }
        /** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
        $gateway = Omnipay::create(static::OMNIPAY_GATEWAY_NAME);
        $gateway->initialize($transaction->getGatewayConfiguration('initialize'));
        try {
            $completePurchase = $gateway->completePurchase();
            $completePurchase->setTransactionToken($transactionToken);
            $transaction->changeStage(Transaction::STAGE_CHARGE_COMPLETING_REQUESTED);
            $completePurchaseResponse = $completePurchase->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_CHARGE_COMPLETING_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);
            $this->getLogger()->error($e);

            return $this->result->error(TransactionProcessingResultInterface::ERROR_CONNECTION);
        }
        $transaction->message = $completePurchaseResponse->getMessage();
        $transaction->code = $completePurchaseResponse->getCode();
        $transaction->reference = $completePurchaseResponse->getTransactionReference();
        if ($completePurchaseResponse->isSuccessful()) {
            $this->switchResultMoneyTransferred(true);
            $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_ACCEPTED, ['receive', 'finalize']);
            $status = TransactionProcessingResultInterface::STATUS_SUCCESS;

        } else {
            $this->switchResultMoneyStart();
            $transaction->changeStage(Transaction::STAGE_FAILED, Transaction::STATUS_FAILED, ['receive']);
            $status = TransactionProcessingResultInterface::STATUS_FAILURE;
        }
        $cardReference = $completePurchaseResponse->getCardReference();
        $registered = $transaction->rememberRegistrationToken($cardReference);

        return $this->result->setStatus($status)->setIsRegistered($registered);
    }
}
