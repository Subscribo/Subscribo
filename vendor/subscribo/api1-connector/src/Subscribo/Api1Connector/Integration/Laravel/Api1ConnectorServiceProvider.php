<?php

namespace Subscribo\Api1Connector\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class Api1ConnectorServiceProvider
 *
 * @package Subscribo\Api1Connector
 */
class Api1ConnectorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register('\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider');
    }
}
