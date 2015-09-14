<?php

namespace Subscribo\Api1\Controllers;

use DateTime;
use InvalidArgumentException;
use Subscribo\Api1\Factories\AddressFactory;
use Subscribo\Api1\Factories\AccountFactory;
use Subscribo\Api1\AbstractBusinessController;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\NoAccountHttpException;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\Address;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\Delivery;
use Subscribo\ModelCore\Models\DeliveryWindow;
use Subscribo\ModelCore\Models\Product;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\Subscription;
use Subscribo\ModelCore\Models\SubscriptionFilter;
use Subscribo\ModelCore\Exceptions\ArgumentValidationException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;
use Subscribo\ApiServerJob\Jobs\Triggered\SalesOrder\SendConfirmationMessage;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class BusinessController
 *
 * @package Subscribo\Api1
 */
class BusinessController extends AbstractBusinessController
{
    use DispatchesJobs;

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

    public function actionGetDelivery($id = null)
    {
        $queryValidationRules = [
            'available' => 'integer',
        ];
        $validated = $this->validateRequestQuery($queryValidationRules);
        $serviceId = $this->context->getServiceId();
        if (is_null($id)) {
            if (empty($validated['available'])) {

                return ['collection' => Delivery::getAllByService($serviceId)];
            } else {
                $limit = intval($validated['available']);

                return ['collection' => Delivery::getAvailableForOrderingByService($serviceId, $limit)];
            }
        }
        $delivery = Delivery::find($id);
        if (empty($delivery)) {
            throw new InstanceNotFoundHttpException();
        }
        if ($delivery->serviceId !== $serviceId) {
            throw new WrongServiceHttpException();
        }

        return ['instance' => $delivery];
    }


    public function actionGetPeriod()
    {
        $subscriptionPeriods = Subscription::getAvailableSubscriptionPeriods($this->context->getService());
        $result = [];
        $localizer = $this->context->getLocalizer()->template('controllers', 'api1')
            ->setPrefix('business.getPeriod.subscriptionPeriods');
        foreach ($subscriptionPeriods as $subscriptionPeriodKey)
        {
            $text = $localizer->transOrDefault($subscriptionPeriodKey, [], null, null, $subscriptionPeriodKey);
            $result[$subscriptionPeriodKey] = $text;
        }

        return ['result' => $result];
    }


    public function actionPostOrder()
    {
        $orderValidationRules = [
            'prices' => 'required|array',
            'discount_codes' => 'array',
            'delivery_id' => 'required|integer',
            'delivery_window_id' => 'integer',
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
        $result = $this->prepareOrder(
            $account,
            $validated['prices'],
            $discountIds,
            $delivery,
            $deliveryWindow,
            $shippingAddress,
            $billingAddress
        );

        return ['result' => $result];
    }


    public function actionPostSubscription()
    {
        $subscriptionPeriods = Subscription::getAvailableSubscriptionPeriods($this->context->getService());
        $subscriptionValidationRules = [
            'subscription_period' => 'in:'.implode(',', $subscriptionPeriods),
            'sales_order_id' => 'integer',
            'discount_codes' => 'array',
            'start' => 'date|after:yesterday'
        ];
        $validated = $this->validateRequestBody($subscriptionValidationRules);
        $salesOrder = $this->retrieveSalesOrder($validated);
        $discountIds = []; //todo implement
        if (empty($salesOrder)) {

            throw new ServerErrorHttpException(501, 'Not Implemented'); //Later also generating subscription without SalesOrder might be implemented
        }
        if (empty($validated['start'])) {
            $start = true;
            $dateSource = $salesOrder;
        } else {
            $start = $validated['start'];
            $dateSource = $start;
        }
        $filters = $this->assembleSubscriptionFilterTypes($validated['subscription_period'], $dateSource);
        $prepareResult = Subscription::prepareSubscriptionFromSalesOrder($salesOrder, $filters, $discountIds, $start);
        $salesOrder->subscription()->associate($prepareResult['subscription']);
        $salesOrder->save();

        return ['result' => $prepareResult];
    }


    public function actionPostMessage()
    {
        $result = [];
        $messageValidationRules = [
            'sales_order_id' => 'integer',
        ];
        $validated = $this->validateRequestBody($messageValidationRules);
        $salesOrder = $this->retrieveSalesOrder($validated);
        if ($salesOrder) {
            $salesOrderNotificationJob = new SendConfirmationMessage($salesOrder);
            $this->dispatch($salesOrderNotificationJob);
            if ($salesOrder->status === SalesOrder::STATUS_ORDERING) {
                $salesOrder->status = SalesOrder::STATUS_ORDERED;
                $salesOrder->save();
            }
            $result['sales_order_message'] = 'job_dispatched';
        } else {

            throw new ServerErrorHttpException(501, 'Not Implemented'); //Later also generating messages for other things might be implemented
        }

        return ['result' => $result];
    }

    /**
     * @param string $subscriptionPeriod
     * @param SalesOrder|Delivery|DateTime|string $dateSource
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function assembleSubscriptionFilterTypes($subscriptionPeriod, $dateSource)
    {
        switch (strval($subscriptionPeriod)) {
            case 'weekly':

                return [];
            case 'biweekly':
                if ($dateSource instanceof DateTime) {
                    $date = $dateSource;
                } elseif ($dateSource instanceof Delivery) {
                    $date = $dateSource->start;
                } elseif ($dateSource instanceof SalesOrder) {
                    $date = $dateSource->delivery->start;
                } elseif (is_string($dateSource)) {
                    $date = new DateTime($dateSource);
                } else {
                    throw new InvalidArgumentException('Wrong dateSource format');
                }
                $weekIsOdd = (intval($date->format('W')) % 2 === 1);

                return $weekIsOdd ? [SubscriptionFilter::TYPE_EVEN_WEEK] : [SubscriptionFilter::TYPE_ODD_WEEK];
        }
        $messageId = 'business.errors.prepareOrder.invalidSubscriptionPeriod';
        $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
        throw new InvalidInputHttpException(['subscription_period' => $message]);
    }

    /**
     * @param Account $account
     * @param array $amountsPerPriceId
     * @param array $discountIds
     * @param Delivery $delivery
     * @param DeliveryWindow $deliveryWindow
     * @param Address $shippingAddress
     * @param Address $billingAddress
     * @param bool $toMakeSubscription
     * @param array $subscriptionFilters
     * @param int|null $currencyId
     * @param int|null|bool $countryId
     * @return array
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function prepareOrder(
        Account $account,
        array $amountsPerPriceId,
        array $discountIds,
        Delivery $delivery,
        DeliveryWindow $deliveryWindow = null,
        Address $shippingAddress = null,
        Address $billingAddress = null,
        $toMakeSubscription = null,
        array $subscriptionFilters = [],
        $currencyId = null,
        $countryId = true
    ) {
        try {
            $result = SalesOrder::prepareSalesOrder(
                $account,
                $amountsPerPriceId,
                $discountIds,
                $delivery,
                $deliveryWindow,
                $shippingAddress,
                $billingAddress,
                $toMakeSubscription,
                $subscriptionFilters,
                $currencyId,
                $countryId,
                SalesOrder::TYPE_MANUAL
            );

            return $result;
        } catch (ArgumentValidationException $e) {
            throw $this->makeInvalidPriceException($e);
        }
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
     * @return SalesOrder|null
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function retrieveSalesOrder(array $input)
    {
        if (empty($input['sales_order_id'])) {

            return null;
        }
        $salesOrder = SalesOrder::find($input['sales_order_id']);
        if ($salesOrder) {

            return $salesOrder;
        }
        $messageId = 'business.errors.postSubscription.salesOrderNotFound';
        $message = $this->context->getLocalizer()->trans($messageId, [], 'api1::controllers');
        throw new InvalidInputHttpException(['sales_order_id' => $message]);
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
