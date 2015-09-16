<?php

namespace Subscribo\Webshop\Http\Controllers;

use Exception;
use Subscribo\Exception\Exceptions\RuntimeException;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use App\Http\Controllers\Controller;
use Subscribo\ApiClientAuth\Connectors\AccountConnector;
use Subscribo\Webshop\Connectors\BusinessConnector;
use Subscribo\Webshop\Connectors\TransactionConnector;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View;
use Illuminate\Http\Request;
use Subscribo\ApiClientAuth\Traits\HandleUserRegistrationTrait;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Psr\Log\LoggerInterface;

/**
 * Class WebshopController
 *
 * @package Subscribo\Webshop
 */
class WebshopController extends Controller
{
    use HandleUserRegistrationTrait;
    use HandleServerRequestExceptionTrait;

    const STAGE_REGISTER_USER = 1;
    const STAGE_CREATE_ORDER = 2;
    const STAGE_CREATE_TRANSACTION = 3;
    const STAGE_EVALUATE_TRANSACTION_RESPONSE = 4;
    const STAGE_CREATE_SUBSCRIPTION = 5;
    const STAGE_CREATE_JOB_SENDING_ORDER_CONFIRMATION_MESSAGE = 6;
    const STAGE_FINAL_REDIRECT = 7;

    const NO_SUBSCRIPTION_OPTION = 'no_subscription';

    protected $sessionKeyBuyProductStage = 'subscribo_webshop_buy_product_stage';
    protected $sessionKeyBuyProductValidatedInput = 'subscribo_webshop_buy_product_validated_input';
    protected $sessionKeyBuyProductProcessingData = 'subscribo_webshop_buy_product_processing_data';
    protected $sessionKeyBuyProductInputForRedirect = 'subscribo_webshop_buy_product_input_for_redirect';

    /**
     * @param BusinessConnector $connector
     * @param LocalizerInterface $localizer
     * @return \Illuminate\View\View
     */
    public function listProducts(BusinessConnector $connector, LocalizerInterface $localizer)
    {
        $products = $connector->getProduct();
        foreach ($products as $key => $product) {
            if (empty($product['name'])) {
                $products[$key]['name'] = $product['identifier'];
            }
        }
        $data = [
            'products' => $products,
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.list'),
        ];

        return view('vendor/subscribo/webshop/product/list', $data);
    }

    /**
     * @param $id
     * @param AccountConnector $accountConnector
     * @param BusinessConnector $businessConnector
     * @param TransactionConnector $transactionConnector
     * @param LocalizerInterface $localizer
     * @param Request $request
     * @param Guard $auth
     * @param Registrar $registrar
     * @param SessionDeposit $sessionDeposit
     * @param CookieDeposit $cookieDeposit
     * @param LoggerInterface $logger
     * @param Store $session
     * @return \Illuminate\View\View
     * @throws \Subscribo\Exception\Exceptions\RuntimeException
     */
    public function getBuyProduct($id, AccountConnector $accountConnector, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $resultInSession = $session->pull($this->sessionKeyServerRequestHandledResult);
        if ($resultInSession) {

            return $this->handleResultInSession($resultInSession, $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
        }
        try {
            $product = $businessConnector->getProduct($id);
            $subscriptionPeriods = $this->acquireSubscriptionPeriods($businessConnector, false, $localizer);
            $transactionGateways = $transactionConnector->getGateway();
            $addresses = $auth->user() ? $accountConnector->getAddress() : [];
            $deliveries = $businessConnector->getAvailableDeliveries();
            $usualDeliveryWindowTypes = $businessConnector->getUsualDeliveryWindowTypes();
            $deliveryWindowTypes = [];
            foreach ($usualDeliveryWindowTypes as $deliveryWindowType)
            {
                $deliveryWindowTypes[$deliveryWindowType['id']] = $deliveryWindowType['name'];
            }
        } catch (Exception $e) {
            throw new RuntimeException('Error in communication with API', 0, $e);
        }
        if (empty($product['name'])) {
            $product['name'] = $product['identifier'];
        }
        $data = [
            'product' => $product,
            'transactionGateways' => $transactionGateways,
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.buy'),
            'addresses' => $addresses,
            'deliveries' => $deliveries,
            'subscriptionPeriods' => $subscriptionPeriods,
            'deliveryWindowTypes' => $deliveryWindowTypes,
        ];

        return view('vendor/subscribo/webshop/product/buy', $data);
    }

    /**
     * @param $id
     * @param BusinessConnector $businessConnector
     * @param TransactionConnector $transactionConnector
     * @param LocalizerInterface $localizer
     * @param Request $request
     * @param Guard $auth
     * @param Registrar $registrar
     * @param SessionDeposit $sessionDeposit
     * @param CookieDeposit $cookieDeposit
     * @param LoggerInterface $logger
     * @param Store $session
     * @return mixed
     */
    public function postBuyProduct($id, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $subscriptionPeriodKeys = $this->acquireSubscriptionPeriods($businessConnector, true);
        $orderValidationRules = [
            'transaction_gateway' => 'required|integer',
            'delivery_id' => 'integer',
            'subscription_period' => 'required| in:'.implode(',', $subscriptionPeriodKeys),
            'address_id' => 'integer',
            'shipping_address_id' => 'integer',
            'billing_is_same' => 'boolean',
            'item_identifier' => 'required|numeric',
        ];
        $usualDeliveryWindowTypes = $businessConnector->getUsualDeliveryWindowTypes();
        if ($usualDeliveryWindowTypes) {
            $windowTypeIds = [];
            foreach($usualDeliveryWindowTypes as $deliveryWindowType) {
                $windowTypeIds[] = $deliveryWindowType['id'];
            }
            $orderValidationRules['delivery_window_type_id'] = 'required|in:'.implode(',', $windowTypeIds);
        }
        $validationRules = $orderValidationRules + Registrar::getAddressValidationRules('', 'address_id');
        $billingValidationRules =  Registrar::getAddressValidationRules('billing_', 'billing_address_id,billing_is_same', 'required_without_all');
        $billingValidationRules['billing_address_id'] = 'integer';
        if (( ! $request->request->get('billing_is_same'))) {
            $validationRules = $validationRules + $billingValidationRules;
        }
        $this->validate($request, $validationRules);
        $validatedData = array_intersect_key($request->request->all(), $validationRules);
        $exceptInput = ['password', 'password_confirmation', '_token'];
        $inputForRedirect = $request->except($exceptInput);

        $session->set($this->sessionKeyBuyProductValidatedInput, $validatedData);
        $session->set($this->sessionKeyBuyProductInputForRedirect, $inputForRedirect);

        return $this->processPostBuyProduct(self::STAGE_REGISTER_USER, [], $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
    }

    /**
     * @param LocalizerInterface $localizer
     * @return \Illuminate\View\View
     */
    public function getSuccess(LocalizerInterface $localizer)
    {
        $data = [
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.success'),
        ];

        return view('vendor/subscribo/webshop/product/success', $data);
    }

    /**
     * @param int $stage
     * @param array $previousProcessResultData
     * @param BusinessConnector $businessConnector
     * @param TransactionConnector $transactionConnector
     * @param LocalizerInterface $localizer
     * @param Request $request
     * @param Guard $auth
     * @param Registrar $registrar
     * @param SessionDeposit $sessionDeposit
     * @param CookieDeposit $cookieDeposit
     * @param LoggerInterface $logger
     * @param Store $session
     * @return mixed
     */
    protected function processPostBuyProduct($stage, array $previousProcessResultData, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $validatedData = $session->get($this->sessionKeyBuyProductValidatedInput);
        $inputForRedirect = $session->get($this->sessionKeyBuyProductInputForRedirect);
        if ($stage <= self::STAGE_REGISTER_USER) {
            if ($auth->guest()) {
                $processingResult = $this->handleUserRegistration($auth, $registrar, $request, $sessionDeposit, $cookieDeposit, $localizer, $logger, []);
            } else {
                $processingResult = [];
            }
        } elseif ($stage == self::STAGE_CREATE_ORDER) {
            $priceId = $validatedData['item_identifier'];
            $data = $validatedData;
            unset($data['transaction_gateway']);
            unset($data['billing_is_same']);
            unset($data['item_identifier']);
            unset($data['subscription_period']);
            $data['prices'] = [$priceId => 1];
            $callback = [$businessConnector, 'postOrder'];
            $genericErrorMessage = function () use ($localizer) {
                return $localizer->trans('errors.orderFailed', [], 'webshop::messages');
            };
            $processingResult = $this->processRemoteCall($callback, [$data], $inputForRedirect, $request->url(), $genericErrorMessage, $logger);
        } elseif ($stage == self::STAGE_CREATE_TRANSACTION) {
            $salesOrderId = $previousProcessResultData['result']['salesOrder']['id'];
            $session->set($this->sessionKeyBuyProductProcessingData, ['sales_order_id' => $salesOrderId]);
            $data = [
                'transaction_gateway' => $validatedData['transaction_gateway'],
                'sales_order_id' => $salesOrderId,
            ];
            $callback = [$transactionConnector, 'postCharge'];
            $genericErrorMessage = function () use ($localizer) {
                return $errorMessage = $localizer->trans('errors.transactionFailed', [], 'webshop::messages');
            };
            $processingResult = $this->processRemoteCall($callback, [$data], $inputForRedirect, $request->url(), $genericErrorMessage, $logger);
        } elseif ($stage == self::STAGE_EVALUATE_TRANSACTION_RESPONSE) {

            $processingResult = $this->processPostBuyProductTransactionFinalization($previousProcessResultData, $inputForRedirect, $request->url());
        } elseif ($stage == self::STAGE_CREATE_SUBSCRIPTION) {
            if ($validatedData['subscription_period'] === self::NO_SUBSCRIPTION_OPTION) {
                $processingResult = []; //Skipping creating subscription
            } else {
                $data = $session->get($this->sessionKeyBuyProductProcessingData);
                $data['subscription_period'] = $validatedData['subscription_period'];
                $callback = [$businessConnector, 'postSubscription'];
                $genericErrorMessage = function () use ($localizer) {
                    return $localizer->trans('errors.subscriptionFailed', [], 'webshop::messages');
                };
                $processingResult = $this->processRemoteCall($callback, [$data], $inputForRedirect, $request->url(), $genericErrorMessage, $logger);
            }
        } elseif ($stage == self::STAGE_CREATE_JOB_SENDING_ORDER_CONFIRMATION_MESSAGE) {
            $data = $session->get($this->sessionKeyBuyProductProcessingData);
            $callback = [$businessConnector, 'postMessage'];
            $genericErrorMessage = function () use ($localizer) {
                return $localizer->trans('errors.confirmationMessageFailed', [], 'webshop::messages');
            };
            $processingResult = $this->processRemoteCall($callback, [$data], $inputForRedirect, $request->url(), $genericErrorMessage, $logger);
        } elseif ($stage >= self::STAGE_FINAL_REDIRECT) {
            $processingResult = [
                'redirect' => redirect()->route('subscribo.webshop.success'),
                'redirectReason' => 'finished',
            ];
        }
        if (isset($processingResult['redirectReason'])) {
            if ('handlingServerRequest' === $processingResult['redirectReason']) {
                $session->set($this->sessionKeyBuyProductStage, $stage);
            } else {
                $session->pull($this->sessionKeyBuyProductStage);
                $session->pull($this->sessionKeyBuyProductValidatedInput);
                $session->pull($this->sessionKeyBuyProductProcessingData);
                $session->pull($this->sessionKeyBuyProductInputForRedirect);
            }
        }
        if (isset($processingResult['redirect'])) {

            return $processingResult['redirect'];
        }

        return $this->processPostBuyProduct($stage + 1, $processingResult, $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
    }

    /**
     * @param callable $callback
     * @param array $parameters
     * @param array $inputForRedirect
     * @param string $backUrl
     * @param \Closure|array|callable $genericErrorMessage
     * @param LoggerInterface|null $logger
     * @return array
     */
    protected function processRemoteCall(callable $callback, array $parameters, array $inputForRedirect, $backUrl, $genericErrorMessage, LoggerInterface $logger = null)
    {
        try {
            $callbackResult = call_user_func_array($callback, $parameters);

        } catch (ServerRequestException $serverRequestException) {

            return [
                'redirect' => $this->handleServerRequestException($serverRequestException, $backUrl),
                'redirectReason' => 'handlingServerRequest',
            ];
        } catch (ValidationErrorsException $validationErrorsException) {
            $errors = $validationErrorsException->getValidationErrors();

            return [
                'redirect' => redirect($backUrl)->withInput($inputForRedirect)->withErrors($errors),
                'redirectReason' => 'handlingValidationErrors',
            ];
        } catch (Exception $genericException) {
            $this->logException($genericException, $logger);
            $errorMessage = value($genericErrorMessage);

            return [
                'redirect' => redirect($backUrl)->withInput($inputForRedirect)->withErrors($errorMessage),
                'redirectReason' => 'handlingGenericException',
            ];
        }

        return ['result' => $callbackResult];
    }

    /**
     * @param array $data
     * @param array $inputForRedirect
     * @param string $backUrl
     * @return array
     */
    protected function processPostBuyProductTransactionFinalization(array $data, array $inputForRedirect, $backUrl)
    {
        if ( ! empty($data['result']['continue'])) {

            return $data;
        }
        $result = $data['result'];
        $errors = empty($result['validationErrors']) ? $result['message'] : $result['validationErrors'];
        if ( ! empty($errors['mobile'])) {
            $errors['phone'] = $errors['mobile'];
        }

        return [
            'redirect' => redirect($backUrl)->withInput($inputForRedirect)->withErrors($errors),
            'redirectReason' => 'handlingFailedTransaction',
        ];
    }

    /**
     * @param $resultInSession
     * @param BusinessConnector $businessConnector
     * @param TransactionConnector $transactionConnector
     * @param LocalizerInterface $localizer
     * @param Request $request
     * @param Guard $auth
     * @param Registrar $registrar
     * @param SessionDeposit $sessionDeposit
     * @param CookieDeposit $cookieDeposit
     * @param LoggerInterface $logger
     * @param Store $session
     * @return mixed
     */
    protected function handleResultInSession($resultInSession, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $stage = $session->pull($this->sessionKeyBuyProductStage);
        if ($stage == 1) {
            $data = $this->handleUserRegistrationResume($resultInSession, $auth, $registrar, $sessionDeposit, $cookieDeposit);
        } else {
            $data = $resultInSession;
        }

        return $this->processPostBuyProduct($stage + 1, $data, $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
    }

    /**
     * @param BusinessConnector $businessConnector
     * @param bool $keysOnly
     * @param LocalizerInterface $localizer
     * @return array
     */
    protected function acquireSubscriptionPeriods(BusinessConnector $businessConnector, $keysOnly, LocalizerInterface $localizer = null)
    {
        $subscriptionPeriods = $businessConnector->getSubscriptionPeriods();
        if ($keysOnly) {
            $result = array_keys($subscriptionPeriods);
            $result[] = self::NO_SUBSCRIPTION_OPTION;

            return $result;
        }
        $result = $subscriptionPeriods;
        $noSubsTrId = 'forms.buy.subscriptionPeriod.select.noSubscription';
        $result[self::NO_SUBSCRIPTION_OPTION] = $localizer->trans($noSubsTrId, [], 'webshop::messages');

        return $result;
    }
}
