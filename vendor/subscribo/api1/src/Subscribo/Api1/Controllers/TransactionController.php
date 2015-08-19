<?php

namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractBusinessController;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\NoAccountHttpException;
use Subscribo\Exception\Exceptions\ServerErrorHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\Currency;
use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\ModelCore\Models\ServiceModule;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\ModelCore\Models\TransactionGateway;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Managers\TransactionProcessingManager;

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
    public function actionPostCharge(PluginResourceManagerInterface $resourceManager)
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
            $countryId = $salesOrder->countryId;
        }
        $service = $this->context->getService();
        $transactionGatewayConfiguration = TransactionGatewayConfiguration::findByAttributes($service->id, $countryId, $currencyId, $transactionGateway->id, true, false);
        if ($salesOrder) {
            $transaction = Transaction::generateFromSalesOrder($salesOrder, $transactionGatewayConfiguration, Transaction::ORIGIN_CUSTOMER);
        } else {

            throw new ServerErrorHttpException(501, 'Not Implemented');
        }
        $driverName = $transaction->transactionGatewayConfiguration->transactionGateway->driver;
        $processingManager = new TransactionProcessingManager($resourceManager, $driverName);

        return $processingManager->charge($transaction);
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
