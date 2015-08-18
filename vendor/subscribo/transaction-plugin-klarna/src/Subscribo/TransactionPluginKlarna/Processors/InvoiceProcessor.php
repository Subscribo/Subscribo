<?php

namespace Subscribo\TransactionPluginKlarna\Processors;

use Exception;
use RuntimeException;
use Omnipay\Omnipay;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessorBase;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Interfaces\QuestionaryFacadeInterface;
use Subscribo\TransactionPluginManager\Utils;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;

/**
 * Class InvoiceProcessor
 *
 * @package Subscribo\TransactionPluginKlarna
 */
class InvoiceProcessor extends TransactionProcessorBase
{
    const OMNIPAY_GATEWAY_NAME = 'Klarna\\Invoice';

    /**
     * @return TransactionProcessingResultBase|TransactionProcessingResultInterface
     * @throws \RuntimeException
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     * @throws \Subscribo\Exception\Exceptions\ServerErrorHttpException
     */
    public function process()
    {
        $status = null;
        $message = null;
        $transaction = $this->transaction;
        $salesOrder = $transaction->salesOrder;
        if (empty($salesOrder)) {
            throw new RuntimeException('Transaction::salesOrder is required for processing via Klarna Invoice');
        }
        $configuration = json_decode($transaction->transactionGatewayConfiguration->configuration, true);
        $initializeData = $configuration['initialize'];
        $authorizeData = empty($configuration['authorize']) ? [] : $configuration['authorize'];
        /** @var \Omnipay\Klarna\InvoiceGateway $gateway */
        $gateway = Omnipay::create(static::OMNIPAY_GATEWAY_NAME);
        $gateway->initialize($initializeData);
        $card = Utils::assembleCardData($salesOrder->billingAddress, $salesOrder->shippingAddress, true)
            + ['email' => $transaction->account->customer->email];
        $questionaries = static::getQuestionariesOnCardForKlarnaInvoice($card, $gateway->getCountry());
        if ($questionaries) {
            $transaction->changeStage(Transaction::STAGE_ADDITIONAL_DATA_REQUESTED);

            return $this->driver->getPluginResourceManager()->interruptByQuestionary($questionaries, $this);
        }
        $authorizeData['transactionId'] = $transaction->hash;
        $authorizeData['amount'] = $transaction->amount;
        $authorizeData['currency'] = $transaction->currency->identifier;
        $localizer = $this->driver->getPluginResourceManager()->getLocalizer();
        $description = Utils::assembleTransactionDescription($salesOrder, $localizer);
        $authorizeData['orderId2'] = Utils::limitStringLength($description, 100, ',');
        $authorizeData['card'] = $card;
        $authorizeData['items'] = Utils::assembleShoppingCart($salesOrder->realizationsInSalesOrders, $salesOrder->discounts, $salesOrder->countryId);
        $transaction->changeStage(Transaction::STAGE_DATA_COLLECTED);

        try {
            $authorizationRequest = $gateway->authorize($authorizeData);
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_REQUESTED);
            $authorizationResponse = $authorizationRequest->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);

            throw new ServerErrorHttpException(502, 'Error when trying to contact API', [], 0, $e);
        }
        $message = $authorizationResponse->getMessage();
        $code = $authorizationResponse->getCode();
        if ($authorizationResponse->isSuccessful()) {
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_RESPONSE_RECEIVED, Transaction::STATUS_ACCEPTED);
            try {
                $captureRequest = $gateway->capture();
                $captureRequest->setReservationNumber($authorizationResponse->getReservationNumber());
                $transaction->changeStage(Transaction::STAGE_CAPTURE_REQUESTED);
                $captureResponse = $captureRequest->send();
            } catch (Exception $e) {
                $transaction->changeStage(Transaction::STAGE_CAPTURE_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);

                throw new ServerErrorHttpException(502, 'Error when trying to contact API', [], 0, $e);
            }
            $message = $captureResponse->getMessage();
            $code = $captureResponse->getCode();
            if ($captureResponse->isSuccessful()) {
                $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_ACCEPTED, ['receive', 'finalize']);

                $status = TransactionProcessingResultInterface::STATUS_SUCCESS;
            } else {
                $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_OWN_RISK, ['receive', 'finalize']);

                $status = TransactionProcessingResultInterface::STATUS_OWN_RISK;
            }
        } elseif ($authorizationResponse->isWaiting()) {
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_RESPONSE_RECEIVED, Transaction::STATUS_WAITING);

            $message = $authorizationResponse->getMessage();
            $status = TransactionProcessingResultInterface::STATUS_WAITING;
        } else {
            $transaction->changeStage(Transaction::STAGE_FAILED, Transaction::STATUS_FAILED, ['receive']);

            $message = $authorizationResponse->getMessage();
            $status = TransactionProcessingResultInterface::STATUS_FAILURE;
        }

        if ($message or $code) {
            $transaction->message = $message;
            $transaction->code = $code;
            $transaction->save();
        }

        return new TransactionProcessingResultBase($this->transaction, $status, $message);
    }

    /**
     * @param array $card
     * @param string $country
     * @return array
     */
    protected static function getQuestionariesOnCardForKlarnaInvoice(array $card, $country)
    {
        $questionaries = [];
        $country = strtoupper($country);
        switch ($country) {
            case 'AT':
            case 'DE':
            case 'NL':
                if (empty($card['birthday'])) {
                    $questionaries[] = QuestionaryFacadeInterface::CODE_CUSTOMER_BIRTH_DATE;
                }
                if (empty($card['gender'])) {
                    $questionaries[] = QuestionaryFacadeInterface::CODE_CUSTOMER_GENDER;
                }
                break;
            default:
                if (empty($card['nationalIdentificationNumber'])) {
                    $questionaries[] =  QuestionaryFacadeInterface::CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER;
                }
                break;
        }

        return $questionaries;
    }
}
