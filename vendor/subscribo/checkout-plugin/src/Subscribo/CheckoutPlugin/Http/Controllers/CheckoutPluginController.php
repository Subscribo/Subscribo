<?php

namespace Subscribo\CheckoutPlugin\Http\Controllers;

use Subscribo\ClientCheckoutCommon\Http\Processors\CheckoutProcessor;
use App\Http\Controllers\Controller;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View;

/**
 * Class CheckoutPluginController
 *
 * @package Subscribo\CheckoutPlugin
 */
class CheckoutPluginController extends Controller
{
    protected $namespaceStub = 'checkout';
    protected $successRoute = 'subscribo.plugin.checkout.success';

    /**
     * @param LocalizerInterface $localizer
     * @return \Illuminate\View\View
     */
    public function listProducts(LocalizerInterface $localizer)
    {
        $data = ['localizer' => $localizer->template('messages', 'checkout-plugin')->setPrefix('template.product.list')];

        return view('subscribo::checkout-plugin.product.list', $data);
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
            'localizer' => $localizer->template('messages', 'checkout-plugin')->setPrefix('template.product.buy'),
        ];

        return view('subscribo::checkout-plugin.product.buy', $data);
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
            'localizer' => $localizer->template('messages', 'checkout-plugin')->setPrefix('template.product.success'),
        ];

        return view('subscribo::checkout-plugin.product.success', $data);
    }
}
