<?php

namespace Subscribo\ClientCheckoutCommon\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class ClientCheckoutCommonServiceProvider
 *
 * @package Subscribo\ClientCheckoutCommon
 */
class ClientCheckoutCommonServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $this->registerViews();
        $this->registerTranslationResources('messages');
    }
}
