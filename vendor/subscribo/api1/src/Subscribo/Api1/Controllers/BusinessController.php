<?php

namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\Factories\AddressFactory;
use Subscribo\Api1\Factories\AccountFactory;
use Subscribo\Api1\AbstractBusinessController;
use Subscribo\Exception\Exceptions\InvalidArgumentException;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\NoAccountHttpException;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\DeliveryWindow;
use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\Order;
use Subscribo\ModelCore\Models\Service;
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
        /** @var Product $product */
        return ['result' => $product->toArrayWithAppropriatePrice($currencyId, $countryId)];
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
        $orderValidationRules = [
            'transaction_gateway' => 'required|max:100',
            'prices' => 'required|array',
            'discount_codes' => 'array',
            'delivery_id' => 'required|integer',
            'delivery_window_id' => 'integer',
            'subscription_period' => 'integer',
            'address_id' => 'integer',
            'billing_address_id' => 'integer',
            'shipping_address_id' => 'integer,'
        ];
        $validationRules = $orderValidationRules + AddressFactory::getValidationRules()
            + AddressFactory::getValidationRules('shipping_') + AddressFactory::getValidationRules('billing_');
        $validated = $this->validateRequestBody($validationRules);
        $service = $this->context->getService();
        $discountIds = []; //todo implement
        $delivery = $this->retrieveDelivery($validated['delivery_id']);
        $deliveryWindow = $this->retrieveDeliveryWindow($validated);
        $account = $this->context->getAccount();
        if (empty($account)) {
            throw new NoAccountHttpException();
        }
        $customer = $account->customer;
        $shippingAddress = $this->retrieveShippingAddress($validated, $customer);
        $billingAddress = $this->retrieveBillingAddress($validated, $customer);
        $this->checkAddresses($validated, $service, $shippingAddress, $billingAddress);
        AccountFactory::addAddressesIfNotPresent($customer, $shippingAddress, $billingAddress);

        $subscriptionPeriod = isset($validated['subscription_period']) ? $validated['subscription_period'] : false;
        $result = $this->prepareOrder($account, $validated['prices'], $discountIds, $delivery, $deliveryWindow, $subscriptionPeriod, $shippingAddress);

        $result['shipping_address'] = $shippingAddress;
        $result['billing_address'] = $billingAddress;

        return ['result' => $result];

        $transactionGateway = TransactionGateway::findByIdentifier($validated['transaction_gateway']);
        $transactionGatewayConfiguration = TransactionGatewayConfiguration::findByAttributes($serviceId, $countryId, $currencyId, $transactionGateway->id, true, false);

        return static::preparePayment($transactionGateway, $transactionGatewayConfiguration, $account, $address, $person);
    }

    private function prepareOrder(Account $account, array $amountsPerPriceId, array $discountIds, Delivery $delivery, DeliveryWindow $deliveryWindow = null, $subscriptionPeriod = false, Address $shippingAddress = null, $currencyId = null, $countryId = true)
    {
        try {
            return Order::prepareOrder($account, $amountsPerPriceId, $discountIds, $delivery, $deliveryWindow, $subscriptionPeriod, $shippingAddress, $currencyId, $countryId, Order::TYPE_MANUAL);
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

    /**
     * @param array $input
     * @return DeliveryWindow|null
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function retrieveDeliveryWindow(array $input)
    {
        if (empty($input['delivery_window_id'])) {
            return null;
        }
        $deliveryWindow = DeliveryWindow::find($input['delivery_window_id']);
        if (empty($deliveryWindow)) {
            $messageId = 'business.errors.prepareOrder.deliveryWindowNotFound';
            $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
            throw new InvalidInputHttpException(['delivery_window_id' => $message]);
        }

        return $deliveryWindow;
    }

    /**
     * @param array $input
     * @param array $prefixes
     * @param Customer $customer
     * @param string $messageKeySwitch 'billing' or 'shipping'
     * @return \Illuminate\Database\Eloquent\Model|null|Address|static
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function tryToRetrieveAddress(array $input, array $prefixes, Customer $customer, $messageKeySwitch)
    {
        foreach ($prefixes as $prefix) {
            $key = $prefix.'address_id';
            if ( ! empty($input[$key])) {
                /** @var Address $address */
                $address = Address::find($input[$key]);
                if (empty($address)) {
                    $messageId = 'business.errors.prepareOrder.'.$messageKeySwitch.'AddressNotFound';
                    $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
                    throw new InvalidInputHttpException([$key => $message]);
                }
                if ($address->customerId !== $customer->id) {
                    $messageId = 'business.errors.prepareOrder.'.$messageKeySwitch.'AddressCustomerMismatch';
                    $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
                    throw new InvalidInputHttpException([$key => $message]);
                }

                return $address;
            }
            try {
                $address = AddressFactory::findOrGenerate($input, $prefix, $customer);
                if ($address) {

                    return $address;
                }
            } catch (\InvalidArgumentException $e) {
                $messageId = 'business.errors.prepareOrder.'.$messageKeySwitch.'CountryNotFound';
                $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
                $messageKey = $prefix.'country';
                throw new InvalidInputHttpException([$messageKey => $message]);
            }
        }

        return null;
    }


    private function retrieveShippingAddress(array $input, Customer $customer)
    {
        $address = $this->tryToRetrieveAddress($input, ['shipping_', ''], $customer, 'shipping');
        if ($address) {

            return $address;
        }

        return $customer->customerConfiguration->defaultShippingAddress;
    }


    private function retrieveBillingAddress(array $input, Customer $customer)
    {
        $address = $this->tryToRetrieveAddress($input, ['billing_', ''], $customer, 'billing');
        if ($address) {

            return $address;
        }

        $billingDetail = $customer->customerConfiguration->defaultBillingDetail;

        return $billingDetail ? $billingDetail->address : null;
    }


    private function checkAddresses(array $input, Service $service, Address $shippingAddress, Address $billingAddress)
    {
        if ($shippingAddress->countryId !== $billingAddress->countryId) {
            $key = isset($input['billing_country']) ? 'billing_country' : 'country';
            $messageId = 'business.errors.prepareOrder.billingCountryInvalid';
            $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
            throw new InvalidInputHttpException([$key => $message]);
        }
        if (( ! $service->isOperatingInCountry($shippingAddress->countryId))) {
            $key = isset($input['shipping_country']) ? 'shipping_country' : 'country';
            $messageId = 'business.errors.prepareOrder.shippingCountryInvalid';
            $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
            throw new InvalidInputHttpException([$key => $message]);
        }
    }
}
