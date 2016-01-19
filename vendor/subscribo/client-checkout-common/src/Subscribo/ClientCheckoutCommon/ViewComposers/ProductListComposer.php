<?php

namespace Subscribo\ClientCheckoutCommon\ViewComposers;

use Subscribo\Api1Connector\Connectors\BusinessConnector;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View\View;

/**
 * Class ProductListComposer
 *
 * @package Subscribo\ClientCheckoutCommon
 */
class ProductListComposer
{
    /**
     * @var BusinessConnector
     */
    protected $businessConnector;

    /**
     * @var LocalizerInterface
     */
    protected $localizer;

    /**
     * @param BusinessConnector $businessConnector
     * @param LocalizerInterface $localizer
     */
    public function __construct(BusinessConnector $businessConnector, LocalizerInterface $localizer)
    {
        $this->businessConnector = $businessConnector;
        $this->localizer = $localizer;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $products = $this->businessConnector->getProduct();
        foreach ($products as $key => $product) {
            if (empty($product['name'])) {
                $products[$key]['name'] = $product['identifier'];
            }
        }
        $localizer = $this->localizer
            ->template('messages', 'client-checkout-common')
            ->setPrefix('template.product.list');

        $view->with('products', $products);
        $view->with('localizer', $localizer);
    }


}
