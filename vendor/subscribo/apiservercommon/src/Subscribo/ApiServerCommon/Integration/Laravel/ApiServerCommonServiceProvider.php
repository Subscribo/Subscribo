<?php

namespace Subscribo\ApiServerCommon\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class ApiServerCommonServiceProvider
 *
 * @package Subscribo\ApiServerCommon
 */
class ApiServerCommonServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->registerTranslationResources('emails');
        $this->registerViews();
    }
}
