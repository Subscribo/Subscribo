<?php

namespace Subscribo\Webshop\Http\Controllers;

use Exception;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use App\Http\Controllers\Controller;
use Subscribo\ApiClientAuth\Connectors\AccountConnector;
use Subscribo\Webshop\Connectors\BusinessConnector;
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

    public function getBuyProduct($id, Guard $auth, AccountConnector $accountConnector, BusinessConnector $businessConnector, LocalizerInterface $localizer)
    {
        $product = $businessConnector->getProduct($id);
        if (empty($product['name'])) {
            $product['name'] = $product['identifier'];
        }
        $transactionGateways = $businessConnector->getGateway();
        $addresses = $auth->user() ? $accountConnector->getAddress() : [];
        $data = [
            'product' => $product,
            'transactionGateways' => $transactionGateways,
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.buy'),
            'addresses' => $addresses,
        ];

        return view('vendor/subscribo/webshop/product/buy', $data);
    }



    public function postBuyProduct($id, BusinessConnector $connector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger)
    {
        $orderValidationRules = [
            'transaction_gateway' => 'required|integer',
            'delivery_id' => 'integer',
            'delivery_window_id' => 'integer',
            'subscription_period' => 'max:10',
            'address_id' => 'integer',
            'shipping_address_id' => 'integer',
            'billing_is_same' => 'boolean',
        ];
        $validationRules = $orderValidationRules + Registrar::getAddressValidationRules('', 'address_id');
        $billingValidationRules =  Registrar::getAddressValidationRules('billing_', 'billing_address_id,billing_is_same', 'required_without_all');
        $billingValidationRules['billing_address_id'] = 'integer';
        if (( ! $request->request->get('billing_is_same'))) {
            $validationRules = $validationRules + $billingValidationRules;
        }
        $this->validate($request, $validationRules);

        if ($auth->guest()) {
            $registrationResult = $this->handleUserRegistration($auth, $registrar, $request, $sessionDeposit, $cookieDeposit, $localizer, $logger, []);
            if (isset($registrationResult['redirect'])) {
                return $registrationResult['redirect'];
            }
        }


        $data = array_intersect_key($request->request->all(), $validationRules);
        $priceId = $request->request->get('item_identifier');
        $data['prices'] = [
                $priceId => 1,
            ];
        $data['subscription_period'] = 1;

        $exceptInput = ['password', 'password_confirmation', '_token'];

        try {
            $result = $connector->postOrder($data);

        } catch(ServerRequestException $serverRequestException) {

            return $this->handleServerRequestException($serverRequestException, $request->url());
        } catch (ValidationErrorsException $validationErrorsException) {
            $errors = $validationErrorsException->getValidationErrors();
            $inputForRedirect = $request->except($exceptInput);

            return redirect($request->url())->withInput($inputForRedirect)->withErrors($errors);
        } catch (Exception $genericException) {
            $this->logException($genericException, $logger);
            $errorMessage = $localizer->trans('errors.orderFailed', [], 'webshop::messages');
            $inputForRedirect = $request->except($exceptInput);

            return redirect($request->url())->withInput($inputForRedirect)->withErrors($errorMessage);
        }
        var_dump($result);

        return 'Buying...';
    }

    public function getPay()
    {

    }

}
