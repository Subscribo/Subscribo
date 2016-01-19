<?php

namespace Subscribo\ClientCheckoutCommon\ViewComposers;

use Subscribo\Localization\Interfaces\LocalizerInterface;
use Illuminate\Contracts\View\View;

/**
 * Class ProductBuySuccessComposer
 * @package Subscribo\ClientCheckoutCommon
 */
class ProductBuySuccessComposer
{
    /** @var \Subscribo\Localization\Interfaces\LocalizerInterface  */
    protected $localizer;

    /**
     * @param LocalizerInterface $localizer
     */
    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $localizer = $this->localizer
            ->template('messages', 'client-checkout-common')
            ->setPrefix('template.product.success');
        $view->with('localizer', $localizer);
    }
}
