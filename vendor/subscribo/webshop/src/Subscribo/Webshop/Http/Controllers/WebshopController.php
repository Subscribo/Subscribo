<?php

namespace Subscribo\Webshop\Http\Controllers;

use Illuminate\Routing\Controller;
use Subscribo\Webshop\Connectors\BusinessConnector;
use Subscribo\Webshop\Connectors\PaymentConnector;

use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View;

class WebshopController extends Controller
{
    public function listProducts(BusinessConnector $business, LocalizerInterface $localizer)
    {
        $products = $business->getProduct();
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

    public function getBuyProduct($id, BusinessConnector $business, PaymentConnector $payment, LocalizerInterface $localizer)
    {
        $product = $business->getProduct($id);
        if (empty($product['name'])) {
            $product['name'] = $product['identifier'];
        }
        $paymentMethods = $payment->getMethod();
        $data = [
            'product' => $product,
            'paymentMethods' => $paymentMethods,
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.buy'),
        ];

        return view('vendor/subscribo/webshop/product/buy', $data);
    }

    public function postBuyProduct($id, BusinessConnector $connector, LocalizerInterface $localizer)
    {

        return 'Buying...';
    }

}
