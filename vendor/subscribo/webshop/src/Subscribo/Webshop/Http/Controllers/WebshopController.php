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

    public function getBuyProduct($id, BusinessConnector $connector, LocalizerInterface $localizer)
    {
        $product = $connector->getProduct($id);
        if (empty($product['name'])) {
            $product['name'] = $product['identifier'];
        }
        $transactionGateways = $connector->getGateway();
        $data = [
            'product' => $product,
            'transactionGateways' => $transactionGateways,
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.buy'),
        ];

        return view('vendor/subscribo/webshop/product/buy', $data);
    }



    public function postBuyProduct($id, BusinessConnector $connector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit, LoggerInterface $logger)
    {
        $orderRules = [
            'transaction_gateway' => 'required|integer',
            'delivery_id' => 'integer',
            'delivery_window_id' => 'integer',
            'subscription_period' => 'max:10',
        ];
        $rules = $orderRules + Registrar::getAddressValidationRules();
        $this->validate($request, $rules);

        if ($auth->guest()) {
            $registrationResult = $this->handleUserRegistration($auth, $registrar, $request, $sessionDeposit, $cookieDeposit, $localizer, $logger, []);
            if (isset($registrationResult['redirect'])) {
                return $registrationResult['redirect'];
            }
        }


        $data = array_intersect_key($request->request->all(), $rules);
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

        return 'Buying...'.var_export($result, true);
    }

    public function getPay()
    {

    }

}
