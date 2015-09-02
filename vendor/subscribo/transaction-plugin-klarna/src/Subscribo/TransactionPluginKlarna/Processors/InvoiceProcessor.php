<?php

namespace Subscribo\TransactionPluginKlarna\Processors;

use Exception;
use RuntimeException;
use Omnipay\Omnipay;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessorBase;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface as ResultInterface;
use Subscribo\TransactionPluginManager\Interfaces\QuestionaryFacadeInterface;
use Subscribo\TransactionPluginManager\Utils;
use Subscribo\ModelCore\Models\Transaction;
use Omnipay\Klarna\InvoiceGateway;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class InvoiceProcessor
 *
 * @package Subscribo\TransactionPluginKlarna
 */
class InvoiceProcessor extends TransactionProcessorBase
{
    const OMNIPAY_GATEWAY_NAME = 'Klarna\\Invoice';

    /**
     * @return TransactionProcessingResultBase|ResultInterface
     * @throws \RuntimeException
     */
    public function doProcess()
    {
        $this->checkInitialStage([
            Transaction::STAGE_ADDITIONAL_DATA_REQUESTED,
            Transaction::STAGE_AUTHORIZATION_RESPONSE_RECEIVED
        ]);
        $this->switchResultMoneyStart();
        $transaction = $this->transaction;
        $configuration = $transaction->getGatewayConfiguration();
        $initializeData = $configuration['initialize'];
        /** @var \Omnipay\Klarna\InvoiceGateway $gateway */
        $gateway = Omnipay::create(static::OMNIPAY_GATEWAY_NAME);
        $gateway->initialize($initializeData);
        if (Transaction::STAGE_AUTHORIZATION_RESPONSE_RECEIVED === $transaction->stage) {
            $reservationNumber = $transaction->getDataToRemember('reservationNumber');
            if (empty($reservationNumber)) {

                throw new RuntimeException('Remembered data reservationNumber missing');
            }
            if (Transaction::STATUS_WAITING !== $transaction->status) {

                throw new RuntimeException('Transaction can not be proceeded further, as it is not in waiting state');
            }

            return $this->capture($gateway, $reservationNumber);
        }
        $salesOrder = $transaction->salesOrder;
        if (empty($salesOrder)) {

            throw new RuntimeException('Transaction::salesOrder is required for processing via Klarna Invoice');
        }

        $card = Utils::assembleCardData($salesOrder->billingAddress, $salesOrder->shippingAddress, true)
            + ['email' => $transaction->account->customer->email];
        $questionaries = static::getQuestionariesOnCardForKlarnaInvoice($card, $gateway->getCountry());
        if ($questionaries) {
            $transaction->changeStage(Transaction::STAGE_ADDITIONAL_DATA_REQUESTED);

            return $this->driver->getPluginResourceManager()->interruptByQuestionary($questionaries, $this);
        }
        $data = empty($configuration['authorize']) ? [] : $configuration['authorize'];

        return $this->authorizeAndCapture($gateway, $data, $card);
    }

    /**
     * @param InvoiceGateway $gateway
     * @param array $data
     * @param array $card
     * @return TransactionProcessingResultBase
     */
    protected function authorizeAndCapture(InvoiceGateway $gateway, array $data, array $card)
    {
        $transaction = $this->transaction;
        $salesOrder = $transaction->salesOrder;
        $data['transactionId'] = $transaction->hash;
        $data['amount'] = $transaction->amount;
        $data['currency'] = $transaction->currency->identifier;
        $localizer = $this->driver->getPluginResourceManager()->getLocalizer();
        $description = Utils::assembleTransactionDescription($salesOrder, $localizer);
        $data['orderId2'] = Utils::limitStringLength($description, 100, ',');
        $data['card'] = $card;
        $data['items'] = Utils::assembleShoppingCart($salesOrder->realizationsInSalesOrders, $salesOrder->discounts, $salesOrder->countryId);

        try {
            $authorizationRequest = $gateway->authorize($data);
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_REQUESTED);
            $authorizationResponse = $authorizationRequest->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);
            $this->getLogger()->error($e);

            return $this->result->error(ResultInterface::ERROR_CONNECTION);
        }
        $reservationNumber = $authorizationResponse->getReservationNumber();
        $transaction->setDataToRemember($reservationNumber, 'reservationNumber');
        if ($authorizationResponse->isSuccessful()) {
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_RESPONSE_RECEIVED, Transaction::STATUS_ACCEPTED);

            return $this->capture($gateway, $reservationNumber);
        }
        if ($authorizationResponse->isWaiting()) {
            $transaction->changeStage(Transaction::STAGE_AUTHORIZATION_RESPONSE_RECEIVED, Transaction::STATUS_WAITING);
            $status = ResultInterface::STATUS_WAITING;
            $reason = ResultInterface::WAITING_FOR_GATEWAY_PROCESSING;

            return $this->result->setStatus($status)->setReason($reason);
        }

        return $this->handleError($authorizationResponse, $gateway->getCountry());
    }

    /**
     * @param InvoiceGateway $gateway
     * @param int|string $reservationNumber
     * @return TransactionProcessingResultBase
     */
    protected function capture(InvoiceGateway $gateway, $reservationNumber)
    {
        $transaction = $this->transaction;
        try {
            $captureRequest = $gateway->capture();
            $captureRequest->setReservationNumber($reservationNumber);
            $transaction->changeStage(Transaction::STAGE_CAPTURE_REQUESTED);
            $this->switchResultMoneyTransferred(false);
            $captureResponse = $captureRequest->send();
        } catch (Exception $e) {
            $transaction->changeStage(Transaction::STAGE_CAPTURE_REQUESTED, Transaction::STATUS_CONNECTION_ERROR);
            $this->getLogger()->error($e);

            return $this->result->error(ResultInterface::ERROR_CONNECTION);
        }
        $transactionReference = $captureResponse->getTransactionReference(); //Invoice Number
        if ($transactionReference) {
            $this->switchResultMoneyTransferred(true);
            $transaction->method = Transaction::METHOD_INVOICE;
            $transaction->reference = $transactionReference;
            $transaction->changeStage(Transaction::STAGE_FINISHED, Transaction::STATUS_ACCEPTED, ['receive', 'finalize']);

            return $this->result->setStatus(ResultInterface::STATUS_SUCCESS);
        }
        $this->switchResultMoneyStart();

        return $this->handleError($captureResponse, $gateway->getCountry());
    }

    /**
     * @param ResponseInterface $response
     * @param string $country
     * @return TransactionProcessingResultBase
     */
    protected function handleError(ResponseInterface $response, $country)
    {
        $code = $response->getCode();
        $message = $response->getMessage();
        $this->result->setMessage($message);
        if ($message or $code) {
            $this->transaction->message = $message;
            $this->transaction->code = $code;
        }
        $this->transaction->changeStage(Transaction::STAGE_FAILED, Transaction::STATUS_FAILED, ['receive']);
        $status = ResultInterface::STATUS_FAILURE;
        $reason = ResultInterface::FAILURE_UNSPECIFIED;
        $invalidInput = static::invalidInputFromCode($code, $country);
        if ($invalidInput) {
            $this->result->setInvalidInputFields($invalidInput);
            $status = ResultInterface::STATUS_ERROR;
            $reason = ResultInterface::ERROR_INPUT;
        }

        return $this->result->setStatus($status)->setReason($reason);
    }

    /**
     * @param int|string $code
     * @param string $country
     * @return array|null
     */
    protected static function invalidInputFromCode($code, $country)
    {
        if (empty($code)) {

            return null;
        }
        $invalidInput = null;
        switch (strval($code)) {
            case '3111':
            case '3206':
                $invalidInput = ['postcode' => true];
                break;
            case '3205':
                $invalidInput = ['city' => true];
                break;
            case '3214':
                $invalidInput = ['gender' => true];
                break;
            case '3215':
                $invalidInput = ['birthday' => true];
                break;
            case '3201':
                $invalidInput = ['mobile' => true];
                break;
            case '3202':
            case '3219':
                $invalidInput = ['phone' => true];
                break;
            case '3302':
                $invalidInput = ['lastName' => true];
                break;
            case '3303':
                $invalidInput = ['firstName' => true];
                break;
            case '3304':
                $invalidInput = ['firstName' => true, 'lastName' => true];
                break;
            //Note: It might be possible to add additional codes,
            //but it is not sure for some of them, whether / which specific field(s) should be highlighted on error
        }

        return $invalidInput;
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
