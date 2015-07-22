<?php

namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractBusinessController;
use Subscribo\Api1\Controllers\AccountController;
use Subscribo\Exception\Exceptions\InvalidArgumentException;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\Order;
use Subscribo\ModelCore\Models\TransactionGateway;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;
use Subscribo\ModelCore\Exceptions\ArgumentValidationException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;

/**
 * Class BusinessController
 *
 * @package Subscribo\Api1
 */
class BusinessController extends AbstractBusinessController
{
    public function actionGetProduct($id = null)
    {
        $serviceId = $this->context->getServiceId();
        $countryId = $this->acquireCountryId();
        $currencyId = $this->acquireCurrencyId($countryId);

        if (is_null($id)) {

            return ['result' => Product::findAllByServiceIdWithPrices($serviceId, $currencyId, $countryId)];
        }
        if (is_numeric($id)) {
            $product = Product::withTranslations()->find($id);
        } else {
            $product = Product::withTranslations()
                ->where('identifier', $id)
                ->where('service_id', $serviceId)
                ->first();
        }
        if (empty($product)) {
            throw new InstanceNotFoundHttpException();
        }
        if ($product->serviceId !== $serviceId) {
            throw new WrongServiceHttpException();
        }

        return ['result' => $product->toArrayWithPrices($currencyId, $countryId)];
    }


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

    public function actionPostOrder()
    {
        $validationRules = [
            'transaction_gateway' => 'required|max:100',
            'prices' => 'required|array',
            'discount_codes' => 'array',
            'delivery_id' => 'required|integer',
            'subscription_period' => 'integer',
        ];
        $validated = $this->validateRequestBody($validationRules);
        $serviceId = $this->context->getServiceId();
        $countryId = $this->acquireCountryId();
        $currencyId = $this->acquireCurrencyId($countryId);

        $transactionGateway = TransactionGateway::findByIdentifier($validated['transaction_gateway']);
        $transactionGatewayConfiguration = TransactionGatewayConfiguration::findByAttributes($serviceId, $countryId, $currencyId, $transactionGateway->id, true, false);

        $account = $this->context->getAccount();
        if (empty($account)) {
            $accountController = new AccountController($this->context);
            $registrationResult = $accountController->actionPostRegistration()['result'];
            $customer = $registrationResult['customer'];
            $address = $registrationResult['address'] ?: $customer->customerConfiguration->defaultDeliveryAddress;
            $person = $registrationResult['person'];
            $account = $registrationResult['account'];
        } else {
            $customer = $account->customer;
            $address = $customer->defaultDeliveryAddress;
            $person = $customer->person;
        }

        $discountIds = [];
        $delivery = $this->retrieveDelivery($validated['delivery_id']);
        $subscriptionPeriod = isset($validated['subscription_period']) ? $validated['subscription_period'] : false;
        $result = $this->prepareOrder($account, $validated['prices'], $discountIds, $currencyId, $countryId, $delivery, $subscriptionPeriod);
        $result['account'] = $account;
        $result['address'] = $address;
        $result['customer'] = $customer;
        $result['person'] = $customer->person;
        return ['result' => $result];

        return static::preparePayment($transactionGateway, $transactionGatewayConfiguration, $account, $address, $person);
    }

    private function prepareOrder(Account $account, array $priceIds, array $discountIds, $currencyId, $countryId, Delivery $delivery, $subscriptionPeriod = false)
    {
        try {
            return Order::prepareOrder($account, $priceIds, $discountIds, $currencyId, $countryId, $delivery, $subscriptionPeriod);
        } catch (ArgumentValidationException $e) {
            throw $this->makeInvalidPriceException($e);
        }
    }

    protected static function preparePayment(
        TransactionGateway $transactionGateway,
        TransactionGatewayConfiguration $transactionGatewayConfiguration,
        Account $account,
        Address $billingAddress,
        Person $billingPerson,
        Address $shippingAddress = null,
        Person $shippingPerson = null
    ) {
        switch ($transactionGateway->identifier) {
            case 'PAY_UNITY-COPY_AND_PAY':
                return static::preparePaymentPayUnityCopyAndPay($transactionGatewayConfiguration, $account, $billingAddress, $billingPerson, $shippingAddress, $shippingPerson);
            case 'KLARNA-INVOICE':
                break;
            default:
                throw new InvalidArgumentException('Unknown transaction gateway');
        }
    }

    protected static function preparePaymentPayUnityCopyAndPay(
        TransactionGatewayConfiguration $transactionGatewayConfiguration,
        Account $account,
        Address $billingAddress,
        Person $billingPerson,
        Address $shippingAddress = null,
        Person $shippingPerson = null
    ) {

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
     * @param ArgumentValidationException $previous
     * @return InvalidInputHttpException
     */
    private function makeInvalidPriceException(ArgumentValidationException $previous)
    {
        $localizer = $this->context->getLocalizer();
        $type = $previous->getType();
        $id = 'business.errors.prepareOrder.'.$type;
        $message = $localizer->trans($id, $previous->getData(), 'api1::controllers');
        $key = $previous->getKey();
        $validationErrors = ['prices['.$key.']' => $message];

        return new InvalidInputHttpException($validationErrors, true, [], true, $previous);
    }

    /**
     * @param int $deliveryId
     * @return Delivery
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function retrieveDelivery($deliveryId)
    {
        $delivery = Delivery::find($deliveryId);
        if (empty($delivery)) {
            $messageId = 'business.errors.prepareOrder.deliveryNotFound';
            $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
            throw new InvalidInputHttpException(['delivery_id' => $message]);
        }

        return $delivery;
    }
}
