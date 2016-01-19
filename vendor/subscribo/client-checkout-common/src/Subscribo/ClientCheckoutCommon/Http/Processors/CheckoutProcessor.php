<?php

namespace Subscribo\ClientCheckoutCommon\Http\Processors;

use Exception;
use InvalidArgumentException;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Subscribo\Api1Connector\Connectors\AccountConnector;
use Subscribo\Api1Connector\Connectors\BusinessConnector;
use Subscribo\Api1Connector\Connectors\TransactionConnector;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Subscribo\ApiClientAuth\Traits\HandleUserRegistrationTrait;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;

/**
 * Class CheckoutProcessor
 *
 * @package Subscribo\ClientCheckoutCommon
 */
class CheckoutProcessor
{
    use HandleServerRequestExceptionTrait;
    use HandleUserRegistrationTrait;
    use ValidatesRequests;

    const STAGE_REGISTER_USER = 1;
    const STAGE_CREATE_ORDER = 2;
    const STAGE_CREATE_TRANSACTION = 3;
    const STAGE_EVALUATE_TRANSACTION_RESPONSE = 4;
    const STAGE_CREATE_SUBSCRIPTION = 5;
    const STAGE_CREATE_JOB_SENDING_ORDER_CONFIRMATION_MESSAGE = 6;
    const STAGE_FINAL_REDIRECT = 7;

    const NO_SUBSCRIPTION_OPTION = 'no_subscription';

    const SESSION_KEY_BUY_PRODUCT_FLASH_MESSAGE = 'subscribo_checkout_buy_product_flash_message';

    protected $sessionKeyBuyProductStage;
    protected $sessionKeyBuyProductValidatedInput;
    protected $sessionKeyBuyProductProcessingData;
    protected $sessionKeyBuyProductInputForRedirect;
    protected $successRoute = true;

    /**
     * @var AccountConnector
     */
    protected $accountConnector;

    /**
     * @var BusinessConnector
     */
    protected $businessConnector;

    /**
     * @var TransactionConnector
     */
    protected $transactionConnector;

    /**
     * @var LocalizerInterface
     */
    protected $localizer;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var Registrar
     */
    protected $registrar;

    /**
     * @var SessionDeposit
     */
    protected $sessionDeposit;

    /**
     * @var CookieDeposit
     */
    protected $cookieDeposit;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Store
     */
    protected $session;

    public function __construct(
        AccountConnector $accountConnector,
        BusinessConnector $businessConnector,
        TransactionConnector $transactionConnector,
        LocalizerInterface $localizer,
        Request $request,
        Guard $auth,
        Registrar $registrar,
        SessionDeposit $sessionDeposit,
        CookieDeposit $cookieDeposit,
        LoggerInterface $logger,
        Store $session
    ) {
        $this->accountConnector = $accountConnector;
        $this->businessConnector = $businessConnector;
        $this->transactionConnector = $transactionConnector;
        $this->localizer = $localizer;
        $this->request = $request;
        $this->auth = $auth;
        $this->registrar = $registrar;
        $this->sessionDeposit = $sessionDeposit;
        $this->cookieDeposit = $cookieDeposit;
        $this->logger = $logger;
        $this->session = $session;
    }

    /**
     * @param string $namespaceStub
     * @param string|bool $successRoute
     * @return bool|\Illuminate\Http\RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function checkSession($namespaceStub = 'checkout', $successRoute = true)
    {
        $this->initialize($namespaceStub, $successRoute);
        $resultInSession = $this->session->pull($this->sessionKeyServerRequestHandledResult);
        if (empty($resultInSession)) {

            return false;
        }

        return $this->handleResultInSession($resultInSession);
    }

    /**
     * @param string $namespaceStub
     * @param bool|string $successRoute
     * @return \Illuminate\Http\RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function handlePostBuyProduct($namespaceStub = 'checkout', $successRoute = true)
    {
        $this->initialize($namespaceStub, $successRoute);
        $subscriptionPeriodKeys = array_keys($this->businessConnector->getSubscriptionPeriods());
        $subscriptionPeriodKeys[] = self::NO_SUBSCRIPTION_OPTION;
        $orderValidationRules = [
            'transaction_gateway' => 'required|integer',
            'delivery_id' => 'integer',
            'subscription_period' => 'required| in:'.implode(',', $subscriptionPeriodKeys),
            'address_id' => 'integer',
            'shipping_address_id' => 'integer',
            'billing_is_same' => 'boolean',
            'item' => 'required|array',
        ];
        $products = $this->businessConnector->getProduct();
        foreach ($products as $product) {
            $orderValidationRules['item.'.$product['price_id']] = 'integer|min:1';
        }
        $usualDeliveryWindowTypes = $this->businessConnector->getUsualDeliveryWindowTypes();
        if ($usualDeliveryWindowTypes) {
            $windowTypeIds = [];
            foreach($usualDeliveryWindowTypes as $deliveryWindowType) {
                $windowTypeIds[] = $deliveryWindowType['id'];
            }
            $orderValidationRules['delivery_window_type_id'] = 'required|in:'.implode(',', $windowTypeIds);
        }
        $validationRules = $orderValidationRules + Registrar::getAddressValidationRules('', 'address_id');
        $billingValidationRules =  Registrar::getAddressValidationRules(
            'billing_',
            'billing_address_id,billing_is_same',
            'required_without_all'
        );
        $billingValidationRules['billing_address_id'] = 'integer';
        if (( ! $this->request->request->get('billing_is_same'))) {
            $validationRules = $validationRules + $billingValidationRules;
        }
        $this->validate($this->request, $validationRules);
        $validatedData = array_intersect_key($this->request->request->all(), $validationRules);
        $exceptInput = ['password', 'password_confirmation', '_token'];
        $inputForRedirect = $this->request->except($exceptInput);
        $totalAmount = 0;
        foreach ($validatedData['item'] as $itemAmount) {
            $totalAmount += $itemAmount;
        }
        if ($totalAmount < 1) {

            return redirect()->back()->withInput($inputForRedirect)
                ->withErrors(['cart' => $this->localizer->trans(
                    'errors.cartEmpty',
                    [],
                    'client-checkout-common::messages'
                )]);
        }
        $this->session->set($this->sessionKeyBuyProductValidatedInput, $validatedData);
        $this->session->set($this->sessionKeyBuyProductInputForRedirect, $inputForRedirect);

        return $this->processPostBuyProduct(self::STAGE_REGISTER_USER);
    }

    /**
     * @param string $namespaceStub
     * @param string|bool $successRoute
     * @throws \InvalidArgumentException
     */
    protected function initialize($namespaceStub = 'checkout', $successRoute = true)
    {
        if (true !== $successRoute and ! is_string($successRoute)) {
            throw new InvalidArgumentException('SuccessRoute have to be true or string');

        }
        $this->successRoute = $successRoute;
        $this->sessionKeyBuyProductStage = 'subscribo_'.$namespaceStub.'_buy_product_stage';
        $this->sessionKeyBuyProductValidatedInput = 'subscribo_'.$namespaceStub.'_buy_product_validated_input';
        $this->sessionKeyBuyProductProcessingData = 'subscribo_'.$namespaceStub.'_buy_product_processing_data';
        $this->sessionKeyBuyProductInputForRedirect = 'subscribo_'.$namespaceStub.'_buy_product_input_for_redirect';
    }

    /**
     * @param int $stage
     * @param array $previousProcessResultData
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    protected function processPostBuyProduct($stage, array $previousProcessResultData = [])
    {
        $localizer = $this->localizer->template('messages', 'client-checkout-common');
        $validatedData = $this->session->get($this->sessionKeyBuyProductValidatedInput);
        if ($stage <= self::STAGE_REGISTER_USER) {
            if ($this->auth->guest()) {
                $processingResult = $this->handleUserRegistration(
                    $this->auth,
                    $this->registrar,
                    $this->request,
                    $this->sessionDeposit,
                    $this->cookieDeposit,
                    $this->localizer,
                    $this->logger,
                    []
                );
            } else {
                $processingResult = [];
            }
        } elseif ($stage == self::STAGE_CREATE_ORDER) {
            $data = $validatedData;
            $data['prices'] = array_filter($data['item']);
            unset($data['item']);
            unset($data['transaction_gateway']);
            unset($data['billing_is_same']);
            unset($data['subscription_period']);
            $callback = [$this->businessConnector, 'postOrder'];
            $genericErrorMessage = function () use ($localizer) {
                return $localizer->trans('errors.orderFailed');
            };
            $processingResult = $this->processRemoteCall($callback, [$data], $genericErrorMessage);
        } elseif ($stage == self::STAGE_CREATE_TRANSACTION) {
            $salesOrderId = $previousProcessResultData['result']['salesOrder']['id'];
            $this->session->set($this->sessionKeyBuyProductProcessingData, ['sales_order_id' => $salesOrderId]);
            $data = [
                'transaction_gateway' => $validatedData['transaction_gateway'],
                'sales_order_id' => $salesOrderId,
            ];
            $callback = [$this->transactionConnector, 'postCharge'];
            $genericErrorMessage = function () use ($localizer) {
                return $errorMessage = $localizer->trans('errors.transactionFailed');
            };
            $processingResult = $this->processRemoteCall($callback, [$data], $genericErrorMessage);
        } elseif ($stage == self::STAGE_EVALUATE_TRANSACTION_RESPONSE) {

            $processingResult = $this->processPostBuyProductTransactionFinalization($previousProcessResultData);
        } elseif ($stage == self::STAGE_CREATE_SUBSCRIPTION) {
            if ($validatedData['subscription_period'] === self::NO_SUBSCRIPTION_OPTION) {
                $processingResult = []; //Skipping creating subscription
            } else {
                $data = $this->session->get($this->sessionKeyBuyProductProcessingData);
                $data['subscription_period'] = $validatedData['subscription_period'];
                $callback = [$this->businessConnector, 'postSubscription'];
                $genericErrorMessage = function () use ($localizer) {
                    return $localizer->trans('errors.subscriptionFailed');
                };
                $processingResult = $this->processRemoteCall($callback, [$data], $genericErrorMessage);
            }
        } elseif ($stage == self::STAGE_CREATE_JOB_SENDING_ORDER_CONFIRMATION_MESSAGE) {
            $data = $this->session->get($this->sessionKeyBuyProductProcessingData);
            $callback = [$this->businessConnector, 'postMessage'];
            $genericErrorMessage = function () use ($localizer) {
                return $localizer->trans('errors.confirmationMessageFailed');
            };
            $processingResult = $this->processRemoteCall($callback, [$data], $genericErrorMessage);
        } elseif ($stage >= self::STAGE_FINAL_REDIRECT) {
            if (true === $this->successRoute) {
                $redirect = redirect()->route($this->request->route()->getName());
                $flashMessage = $localizer->trans('success.flash');
                $this->session->flash(self::SESSION_KEY_BUY_PRODUCT_FLASH_MESSAGE, $flashMessage);
            } elseif (is_string($this->successRoute)) {
                $redirect = redirect()->route($this->successRoute);
            } else {
                throw new Exception('SuccessRoute have to be true or string');
            }
            $processingResult = [
                'redirect' => $redirect,
                'redirectReason' => 'finished',
            ];
        }
        if (isset($processingResult['redirectReason'])) {
            if ('handlingServerRequest' === $processingResult['redirectReason']) {
                $this->session->set($this->sessionKeyBuyProductStage, $stage);
            } else {
                $this->session->pull($this->sessionKeyBuyProductStage);
                $this->session->pull($this->sessionKeyBuyProductValidatedInput);
                $this->session->pull($this->sessionKeyBuyProductProcessingData);
                $this->session->pull($this->sessionKeyBuyProductInputForRedirect);
            }
        }
        if (isset($processingResult['redirect'])) {

            return $processingResult['redirect'];
        }

        return $this->processPostBuyProduct($stage + 1, $processingResult);
    }

    /**
     * @param callable $callback
     * @param array $parameters
     * @param \Closure|array|callable $genericErrorMessage
     * @return array
     */
    protected function processRemoteCall(callable $callback, array $parameters, $genericErrorMessage)
    {
        $inputForRedirect = $this->session->get($this->sessionKeyBuyProductInputForRedirect);
        $backUrl = $this->request->url();

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
            $this->logException($genericException, $this->logger);
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
     * @return array
     */
    protected function processPostBuyProductTransactionFinalization(array $data)
    {
        if ( ! empty($data['result']['continue'])) {

            return $data;
        }
        $result = $data['result'];
        $errors = empty($result['validationErrors']) ? $result['message'] : $result['validationErrors'];
        if ( ! empty($errors['mobile'])) {
            $errors['phone'] = $errors['mobile'];
        }
        $inputForRedirect = $this->session->get($this->sessionKeyBuyProductInputForRedirect);
        $backUrl = $this->request->url();

        return [
            'redirect' => redirect($backUrl)->withInput($inputForRedirect)->withErrors($errors),
            'redirectReason' => 'handlingFailedTransaction',
        ];
    }

    /**
     * @param $resultInSession
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleResultInSession($resultInSession)
    {
        $stage = $this->session->pull($this->sessionKeyBuyProductStage);
        if ($stage == 1) {
            $data = $this->handleUserRegistrationResume(
                $resultInSession,
                $this->auth,
                $this->registrar,
                $this->sessionDeposit,
                $this->cookieDeposit
            );
            if (empty($data)) {
                $this->logger->warning('User registration resume result empty');
                $data = [];
            }
        } else {
            $data = $resultInSession;
        }

        return $this->processPostBuyProduct($stage + 1, $data);
    }
}
