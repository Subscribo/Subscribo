<?php

namespace Subscribo\Webshop\Http\Controllers;

use Subscribo\ClientCheckoutCommon\Http\Processors\CheckoutProcessor;
use App\Http\Controllers\Controller;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View;

/**
 * Class WebshopController
 *
 * @package Subscribo\Webshop
 */
class WebshopController extends Controller
{
    protected $namespaceStub = 'webshop';
    protected $successRoute = 'subscribo.webshop.success';

    /**
     * @param LocalizerInterface $localizer
     * @return \Illuminate\View\View
     */
    public function listProducts(LocalizerInterface $localizer)
    {
        $data = ['localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.list')];

        return view('subscribo::webshop.product.list', $data);
    }

    /**
     * @param CheckoutProcessor $checkoutProcessor
     * @param LocalizerInterface $localizer
     * @param string|int|null $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getBuyProduct(CheckoutProcessor $checkoutProcessor, LocalizerInterface $localizer, $id = null)
    {
        $checkedSessionResult = $checkoutProcessor->checkSession($this->namespaceStub, $this->successRoute);
        if ($checkedSessionResult) {

            return $checkedSessionResult;
        }
        $data = [
            'localizer' => $localizer->template('messages', 'webshop')->setPrefix('template.product.buy'),
        ];

        return view('subscribo::webshop.product.buy', $data);
    }

    /**
     * @param CheckoutProcessor $checkoutProcessor
     * @param string|int|null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBuyProduct(CheckoutProcessor $checkoutProcessor, $id = null)
    {
        return $checkoutProcessor->handlePostBuyProduct($this->namespaceStub, $this->successRoute);
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
}
