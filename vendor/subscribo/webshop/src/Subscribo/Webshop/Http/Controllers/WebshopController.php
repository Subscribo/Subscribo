<?php

namespace Subscribo\Webshop\Http\Controllers;

use Exception;
use Subscribo\RestClient\Exceptions\ServerRequestException;
use Subscribo\RestClient\Exceptions\ValidationErrorsException;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\ApiClientAuth\Registrar;
use Subscribo\Localization\Deposits\SessionDeposit;
use Subscribo\Localization\Deposits\CookieDeposit;
use Illuminate\Routing\Controller;
use Subscribo\Webshop\Connectors\BusinessConnector;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View;
use Illuminate\Http\Request;
use Subscribo\Localization\LocaleUtils;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestExceptionTrait;
use Subscribo\ApiClientAuth\Connectors\AccountConnector;


class WebshopController extends Controller
{
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



    public function postBuyProduct($id, BusinessConnector $connector, LocalizerInterface $localizer, Request $request, Guard $auth, Registrar $registrar, SessionDeposit $sessionDeposit, CookieDeposit $cookieDeposit)
    {
        // $rules = $registrar->getValidationRules();
        // $this->validate($request, $rules);

        $data = $request->except('item_identifier');
        $priceId = $request->request->get('item_identifier');
        $data['prices'][$priceId] = 1;



        try {
            $result = $connector->postOrder($data);
            $account = $registrar->assembleModel(AccountConnector::assembleResult(['result' => $result], 'result'));
            if (empty($account)) {
                throw new Exception('Empty account.');
            }
        } catch (ServerRequestException $e) {
            return $this->handleServerRequestException($e, $request->url());

        } catch (ValidationErrorsException $e) {
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors($e->getValidationErrors());
        } catch (Exception $e) {
            $this->logException($e);
            $errorMessage = $localizer->trans('errors.registrationFailed', [], 'apiclientauth::messages');
            return redirect()
                ->refresh()
                ->withInput($request->only('email', 'name'))
                ->withErrors($errorMessage);
        }
        $auth->login($account);
        LocaleUtils::rememberLocaleForUser($account, $sessionDeposit, $cookieDeposit);

        return 'Buying...'.var_export($result);

        return redirect($this->redirectPath());
    }

    public function getPay()
    {

    }

}
