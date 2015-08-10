<?php

namespace Subscribo\Api1\Controllers;

use Exception;
use Subscribo\Api1\Exceptions\RuntimeException;
use Subscribo\Api1\AbstractBusinessController;
use Subscribo\Api1\Context;
use Subscribo\Exception\Exceptions\InvalidArgumentException;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\NoAccountHttpException;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Currency;
use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\AccountTransactionGatewayToken;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\ServiceModule;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\ModelCore\Models\TransactionGateway;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Omnipay\Omnipay;
use Subscribo\Omnipay\Shared\CreditCard;
use Subscribo\RestCommon\Widget;
use Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException;

/**
 * Class TransactionController
 *
 * @package Subscribo\Api1
 */
class TransactionController extends AbstractBusinessController
{
    public function actionGetGateway($id = null)
    {
        $serviceId = $this->context->getServiceId();
        $countryId = $this->acquireCountryId();
        $currencyId = $this->acquireCurrencyId($countryId);

        if (is_null($id)) {

            return ['result' => TransactionGateway::findAvailable($serviceId, $countryId, $currencyId)];
        }
        $transactionGateway = TransactionGateway::findByIdentifier($id);

        if (empty($transactionGateway)) {
            throw new InstanceNotFoundHttpException();
        }

        return ['result' => $transactionGateway];
    }

    /**
     * New DEBIT transaction
     */
    public function actionPostCharge()
    {
        $chargeValidationRules = [
            'transaction_gateway' => 'required',
            'country' => 'required_without:sales_order_id',
            'sales_order_id' => 'integer|required_without:amount',
            'amount' => 'numeric|required_without:sales_order_id',
            'currency' => 'required_with:amount',
        ];
        $validated = $this->validateRequestBody($chargeValidationRules);

        $transactionGateway = TransactionGateway::findByIdentifier($validated['transaction_gateway']);
        if (empty($transactionGateway)) {
            throw $this->assembleError('transaction_gateway', 'transactionGatewayNotFound');
        }
        $account = $this->context->getAccount();
        if (empty($account)) {
            throw new NoAccountHttpException();
        }
        if (empty($validated['country'])) {
            $country = null;
            $countryId = null;
        } else {
            $country = Country::findByIdentifier($validated['country']);
            if (empty($country)) {
                throw $this->assembleError('country', 'countryNotFound');
            }
            $countryId = $country->id;
        }
        if (empty($validated['currency'])) {
            $currency = null;
            $currencyId = null;
        } else {
            /** @var Currency $currency */
            $currency = Currency::findByIdentifier($validated['currency']);
            if (empty($currency)) {
                throw $this->assembleError('currency', 'currencyNotFound');
            }
            $currencyId = $currency->id;
        }
        if (empty($validated['amount'])) {
            $amount = false;
        } else {
            if ( ! $currency->checkAmount($validated['amount'], false, false)) {
                throw $this->assembleError('amount', 'invalidAmount');
            }
            $amount = $currency->normalizeAmount($validated['amount']);
        }
        if (empty($validated['sales_order_id'])) {
            $salesOrder = null;
        } else {
            /** @var SalesOrder $salesOrder */
            $salesOrder = SalesOrder::find($validated['sales_order_id']);
            if (empty($salesOrder)) {
                throw $this->assembleError('sales_order_id', 'salesOrderNotFound');
            }
            if ($currency and ($salesOrder->currencyId !== $currency->id)) {
                throw $this->assembleError('currency', 'currencyDoesNotMatchWithSalesOrder');
            }
            if ($country and ($salesOrder->countryId !== $country->id)) {
                throw $this->assembleError('country', 'countryDoesNotMatchWithSalesOrder');
            }
            if (($amount !== false) and ($amount !== $currency->normalizeAmount($salesOrder->grossSum))) {
                throw $this->assembleError('amount', 'amountDoesNotMatchWithSalesOrder');
            }
            $currencyId = $salesOrder->currencyId;
            $countryId =$salesOrder->countryId;
        }
        $service = $this->context->getService();
        $transactionGatewayConfiguration = TransactionGatewayConfiguration::findByAttributes($service->id, $countryId, $currencyId, $transactionGateway->id, true, false);
        if ($salesOrder) {
            $transaction = Transaction::generateFromSalesOrder($salesOrder, $transactionGatewayConfiguration, Transaction::ORIGIN_CUSTOMER);
        } else {

            throw new ServerErrorHttpException(501, 'Not Implemented');
        }

        $processed = $this->processChargeTransaction($transaction);

        return ['result' => ['transaction' => $transaction, 'processed' => $processed]];
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @param array $answer
     * @param $action
     * @param Context $context
     * @param Widget $widget
     * @return array
     * @throws \Subscribo\Api1\Exceptions\RuntimeException
     * @throws \Subscribo\Exception\Exceptions\ServerErrorHttpException
     */
    public function resumeProcessChargePayUnityCopyAndPay(ActionInterruption $actionInterruption, array $answer, $action, Context $context, Widget $widget)
    {
        /** @var Transaction $transaction */
        $transaction = Transaction::find($actionInterruption->extraData['transactionId']);
        if (empty($transaction)) {
            throw new RuntimeException('Remembered transaction not found');
        }
        if (empty($answer['request']['query']['token'])) {
            $transaction->changeStage($transaction::STAGE_CHARGE_RESPONSE_RECEIVED, $transaction::STATUS_RESPONSE_ERROR);

            throw new ServerErrorHttpException(502, 'Wrong response from API contacted');
        }
        if (strval($actionInterruption->extraData['transactionToken']) !== strval($answer['request']['query']['token'])) {
            $transaction->changeStage($transaction::STAGE_CHARGE_RESPONSE_RECEIVED, $transaction::STATUS_RESPONSE_ERROR);

            throw new ServerErrorHttpException(500, 'Token does not match with the one in internal system');
        }
        $configuration = json_decode($transaction->transactionGatewayConfiguration->configuration, true);
        $initializeData = $configuration['initialize'];
        /** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
        $gateway = Omnipay::create($transaction->transactionGatewayConfiguration->transactionGateway->gateway);
        $gateway->initialize($initializeData);
        try {
            $completePurchase = $gateway->completePurchase();
            $completePurchase->setTransactionToken($actionInterruption->extraData['transactionToken']);
            $transaction->changeStage($transaction::STAGE_CHARGE_COMPLETING_REQUESTED);
            $completePurchaseResponse = $completePurchase->send();
        } catch (Exception $e) {
            $transaction->changeStage($transaction::STAGE_CHARGE_COMPLETING_REQUESTED, $transaction::STATUS_CONNECTION_ERROR);

            throw new ServerErrorHttpException(502, 'Error when trying to contact API', [], 0, $e);
        }
        $transaction->message = $completePurchaseResponse->getMessage();
        $transaction->code = $completePurchaseResponse->getCode();
        $transaction->reference = $completePurchaseResponse->getTransactionReference();
        if ($completePurchaseResponse->isSuccessful()) {
            $transaction->changeStage($transaction::STAGE_FINISHED, $transaction::STATUS_ACCEPTED, ['receive', 'finalize']);
            $status = 'accepted';
        } elseif ($completePurchaseResponse->isWaiting()) {
            $transaction->changeStage($transaction::STAGE_CHARGE_COMPLETING_RESPONSE_RECEIVED, $transaction::STATUS_WAITING);
            $status = 'waiting';
        } else {
            $transaction->changeStage($transaction::STAGE_CHARGE_COMPLETING_RESPONSE_RECEIVED, $transaction::STATUS_DENIED);
            $status = 'denied';
        }
        $registrationToken = $completePurchaseResponse->getAccountRegistration();
        $registered = $this->rememberRegistrationToken($transaction, $registrationToken) ? true : false;

        return [
            'result' => [
                'transaction' => $transaction,
                'processed' => [
                    'status' => $status,
                    'message' => $completePurchaseResponse->getMessage(),
                    'code' => $completePurchaseResponse->getCode(),
                    'registered' => $registered,
                ]
            ]
        ];
    }

    protected static function rememberRegistrationToken(Transaction $transaction, $registrationToken)
    {
        if (empty($registrationToken)) {

            return null;
        }
        $account = $transaction->account;
        if (empty($account)) {

            return null;
        }

        return AccountTransactionGatewayToken::addToken($account->id, $transaction->transactionGatewayConfigurationId, $registrationToken);
    }

    protected function processChargeTransaction(Transaction $transaction)
    {
        $transaction->changeStage($transaction::STAGE_INITIALIZED);

        switch ($transaction->transactionGatewayConfiguration->transactionGateway->identifier) {
            case 'PAY_UNITY-COPY_AND_PAY':
                return $this->processChargePayUnityCopyAndPay($transaction);
            case 'KLARNA-INVOICE':
                break;
            default:
                throw new InvalidArgumentException('Unknown transaction gateway');
        }
    }

    protected function processChargePayUnityCopyAndPay(Transaction $transaction)
    {
        $widget = new Widget();
        $actionInterruption = $this->provideActionInterruptionFactory()
            ->makeActionInterruption('resumeProcessChargePayUnityCopyAndPay', [], $widget, false);

        $configuration = json_decode($transaction->transactionGatewayConfiguration->configuration, true);
        $initializeData = $configuration['initialize'];
        $purchaseData = $configuration['purchase'];
        /** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
        $gateway = Omnipay::create($transaction->transactionGatewayConfiguration->transactionGateway->gateway);
        $gateway->initialize($initializeData);
        $purchaseData['amount'] = $transaction->amount;
        $purchaseData['currency'] = $transaction->currency->identifier;
        $purchaseData['transactionId'] = $transaction->hash;
        $purchaseData['returnUrl'] = static::assembleWidgetReturnUrl($transaction->service, $widget->hash);
        $salesOrder = $transaction->salesOrder;
        if ($salesOrder) {
            $description = static::assembleTransactionDescription($salesOrder, $this->context->getLocalizer());
            $purchaseData['description'] = static::limitStringLength($description, 120, ',');
            $address = $salesOrder->billingAddress ?: $salesOrder->shippingAddress;
            if ($address) {
                $purchaseData['card'] = static::assembleAddressData($address, $salesOrder->shippingAddress);
            }
        }
        try {
            $purchase = $gateway->purchase($purchaseData);
            $transaction->changeStage($transaction::STAGE_PREPARATION_REQUESTED);
            $purchaseResponse = $purchase->send();
        } catch (Exception $e) {
            $transaction->changeStage($transaction::STAGE_PREPARATION_REQUESTED, $transaction::STATUS_CONNECTION_ERROR);

            throw new ServerErrorHttpException(502, 'Error when trying to contact API', [], 0, $e);
        }
        if ( ! $purchaseResponse->isTransactionToken()) {
            $transaction->changeStage($transaction::STAGE_PREPARATION_RESPONSE_RECEIVED, $transaction::STATUS_RESPONSE_ERROR, null, 'No token');

            throw new ServerErrorHttpException(502, 'Wrong response from API contacted');
        }
        $extraData = [
            'transactionId' => $transaction->id,
            'transactionToken' => $purchaseResponse->getTransactionToken(),
        ];
        $widget->content = $purchaseResponse->getWidget()->render();
        $actionInterruption->extraData = $extraData;
        $actionInterruption->serverRequest = $widget;
        $actionInterruption->save();

        $transaction->changeStage($transaction::STAGE_PREPARATION_RESPONSE_RECEIVED, $transaction::STATUS_WIDGET_PROVIDED);

        throw new WidgetServerRequestHttpException($widget);
    }

    protected static function assembleWidgetReturnUrl(Service $service, $hash)
    {
        $parameters = ['hash' => $hash];
        $url = ServiceModule::retrieveUri($service, ServiceModule::MODULE_WIDGET, $parameters);
        if (0 === strpos($url, '/')) {
            if (empty($service->url)) {
                throw new RuntimeException('Provided service does not have url defined');
            }
            $url = ($service->url).$url;
        }
        return $url;
    }

    protected static function limitStringLength($input, $limit = 120, $delimiter = ' ', $ending = '...')
    {
        if ($delimiter) {
            $parts = explode($delimiter, $input);
        } else {
            $parts = mb_split('/./', $input);
        }
        $result = '';
        $first = true;
        foreach ($parts as $part) {
            if ((strlen($result) + strlen($part) + 1) > $limit) {

                return $result.$ending;
            }
            if ($first) {
                $first = false;
            } else {
                $result .= ' ';
            }
            $result .= $part;
        }

        return $result;
    }

    protected static function assembleTransactionDescription(SalesOrder $salesOrder, LocalizerInterface $localizer)
    {
        $description = $localizer->trans('transaction.description.intro');
        $first = true;
        foreach ($salesOrder->realizationsInSalesOrders as $realization)
        {
            if ($first) {
                $first = false;
            } else {
                $description .= ',';
            }
            $description .= ' '.$realization->price->product->name;
            if (strval($realization->amount) !== '1') {
                $description .= ' x '.$realization->amount;
            }
        }
        return $description;
    }

    protected static function assembleCardData(Address $billingAddress, Address $shippingAddress = null)
    {
        $shippingAddress = $shippingAddress ?: $billingAddress;
        $result = static::assembleAddressData($billingAddress)  + static::assembleAddressData($shippingAddress, false);

        return $result;
    }

    /**
     * @param Address $address
     * @param bool $billing
     * @param Person $person
     * @return array
     */
    protected static function assembleAddressData(Address $address, $billing = true, Person $person = null)
    {
        $prefix = $billing ? 'billing' : 'shipping';
        $person = $person ?: $address->person;
        $data = [
            'firstName' => $person->firstName.($person->middleNames ? ' '.$person->middleNames : ''),
            'lastName' => $person->lastName,
            'title' => $person->prefix,
            'company' => $address->companyName,
            'address1' => $address->compileStreetLine(),
            'city' => $address->city,
            'postcode' => $address->postCode,
            'state' => $address->state ? $address->state->identifier : null,
            'country' => $address->country->identifier,
        ];
        $result = [];
        foreach ($data as $key => $value) {
            if ($value) {
                $resultKey = $prefix ? ($prefix.ucfirst($key)) : $key;
                $result[$resultKey] = $value;
            }
        }
        if ($billing and (Person::GENDER_MAN === $person->gender)) {
            $result['gender'] = CreditCard::GENDER_MALE;
        }
        if ($billing and (Person::GENDER_WOMAN === $person->gender)) {
            $result['gender'] = CreditCard::GENDER_FEMALE;
        }
        if ($billing and $person->dateOfBirth) {
            $result['birthday'] = $person->dateOfBirth;
        }

        return $result;
    }


    protected static function preparePaymentKlarnaInvoice(
        TransactionGatewayConfiguration $transactionGatewayConfiguration,
        Account $account,
        Address $billingAddress,
        Person $billingPerson,
        Address $shippingAddress = null,
        Person $shippingPerson = null
    ) {

    }

    /**
     * @param string $fieldKey
     * @param string $messageKey
     * @param string $method
     * @return InvalidInputHttpException
     */
    private function assembleError($fieldKey, $messageKey, $method = 'postCharge')
    {
        $localizer = $this->context->getLocalizer();
        $id = 'transaction.errors.'.$method.'.'.$messageKey;
        $message = $localizer->trans($id, [], 'api1::controllers');

        return new InvalidInputHttpException([$fieldKey => $message]);
    }
}
