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


class WebshopController extends Controller
{
    use HandleUserRegistrationTrait;
    use HandleServerRequestExceptionTrait;

    protected $sessionKeyBuyProductStage = 'subscribo_webshop_buy_product_stage';
    protected $sessionKeyBuyProductValidatedInput = 'subscribo_webshop_buy_product_validated_input';
    protected $sessionKeyBuyProductInputForRedirect = 'subscribo_webshop_buy_product_input_for_redirect';


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

    public function getBuyProduct($id, AccountConnector $accountConnector, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $resultInSession = $session->get($this->sessionKeyServerRequestHandledResult);
        if ($resultInSession) {

            return $this->handleResultInSession($resultInSession, $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
        }
        try {
            $product = $businessConnector->getProduct($id);
            $transactionGateways = $transactionConnector->getGateway();
            $addresses = $auth->user() ? $accountConnector->getAddress() : [];
            $deliveries = $businessConnector->getAvailableDeliveries();
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
        ];

        return view('vendor/subscribo/webshop/product/buy', $data);
    }


    public function postBuyProduct($id, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $orderValidationRules = [
            'transaction_gateway' => 'required|integer',
            'delivery_id' => 'integer',
            'delivery_window_id' => 'integer',
            'subscription_period' => 'max:10',
            'address_id' => 'integer',
            'shipping_address_id' => 'integer',
            'billing_is_same' => 'boolean',
            'item_identifier' => 'required|numeric',
        ];
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
        $session->set($this->sessionKeyBuyProductStage, 1);

        if ($auth->guest()) {
            $registrationResult = $this->handleUserRegistration($auth, $registrar, $request, $sessionDeposit, $cookieDeposit, $localizer, $logger, []);
            if (isset($registrationResult['redirect'])) {
                return $registrationResult['redirect'];
            }
        }

        return $this->processPostBuyProduct(2, [], $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
    }

    protected function processPostBuyProduct($stage, $previousProcessResultData = [], BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $validatedData = $session->get($this->sessionKeyBuyProductValidatedInput);
        $inputForRedirect = $session->get($this->sessionKeyBuyProductInputForRedirect);

        if ($stage < 3) {
            $priceId = $validatedData['item_identifier'];
            $data = $validatedData;
            unset($data['transaction_gateway']);
            unset($data['billing_is_same']);
            unset($data['item_identifier']);
            $data['prices'] = [$priceId => 1];
            $data['subscription_period'] = 1;
            $processingResult = $this->processPostBuyProductPostOrder($data, $inputForRedirect, $businessConnector, $localizer, $request, $logger);
        } elseif ($stage === 3) {
            $data = [
                'transaction_gateway' => $validatedData['transaction_gateway'],
                'sales_order_id' => $previousProcessResultData['result']['salesOrder']['id'],
            ];
            $processingResult = $this->processPostBuyProductPostTransaction($data, $inputForRedirect, $transactionConnector, $localizer, $request, $logger);
        } elseif ($stage > 3) {
            ob_start();
            var_dump($previousProcessResultData);
            $result = ob_get_contents();
            ob_end_clean();
            return 'Buying...'."<br>\n".$result;
        }
        if (isset($processingResult['redirect'])) {
            $session->set($this->sessionKeyBuyProductStage, $stage);

            return $processingResult['redirect'];
        }

        return $this->processPostBuyProduct($stage + 1, $processingResult, $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
    }

    protected function processPostBuyProductPostOrder(array $data, array $inputForRedirect, BusinessConnector $businessConnector, LocalizerInterface $localizer, Request $request, LoggerInterface $logger)
    {
        try {
            $postOrderResult = $businessConnector->postOrder($data);

        } catch(ServerRequestException $serverRequestException) {

            return ['redirect' => $this->handleServerRequestException($serverRequestException, $request->url())];
        } catch (ValidationErrorsException $validationErrorsException) {
            $errors = $validationErrorsException->getValidationErrors();

            return ['redirect' => redirect($request->url())->withInput($inputForRedirect)->withErrors($errors)];
        } catch (Exception $genericException) {
            $this->logException($genericException, $logger);
            $errorMessage = $localizer->trans('errors.orderFailed', [], 'webshop::messages');

            return ['redirect' => redirect($request->url())->withInput($inputForRedirect)->withErrors($errorMessage)];
        }

        return ['result' => $postOrderResult];
    }

    protected function processPostBuyProductPostTransaction(array $data, array $inputForRedirect, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, LoggerInterface $logger)
    {
        try {

            $postChargeResult = $transactionConnector->postCharge($data);

        } catch(ServerRequestException $serverRequestException) {

            return ['redirect' => $this->handleServerRequestException($serverRequestException, $request->url())];
        } catch (ValidationErrorsException $validationErrorsException) {
            $errors = $validationErrorsException->getValidationErrors();

            return ['redirect' => redirect($request->url())->withInput($inputForRedirect)->withErrors($errors)];
        } catch (Exception $genericException) {
            $this->logException($genericException, $logger);
            $errorMessage = $localizer->trans('errors.transactionFailed', [], 'webshop::messages');

            return ['redirect' => redirect($request->url())->withInput($inputForRedirect)->withErrors($errorMessage)];
        }

        return ['result' => $postChargeResult];
    }


    protected function handleResultInSession($resultInSession, BusinessConnector $businessConnector, TransactionConnector $transactionConnector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger, Store $session)
    {
        $stage = $session->pull($this->sessionKeyBuyProductStage);
        if ($stage === 1) {
            $this->handleUserRegistrationResume($resultInSession, $auth, $registrar, $sessionDeposit, $cookieDeposit);

            return $this->processPostBuyProduct(2, [], $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
        }

        return $this->processPostBuyProduct($stage + 1, $resultInSession, $businessConnector, $transactionConnector, $localizer, $request, $auth, $registrar, $sessionDeposit, $cookieDeposit, $logger, $session);
    }

}
