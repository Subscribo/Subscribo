<?php namespace Subscribo\Billing\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class BillingServiceProvider
 *
 * @package Subscribo\Billing
 */
class BillingServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (class_exists('\\Subscribo\\TransactionPluginKlarna\\Integration\\Laravel\\TransactionPluginKlarnaServiceProvider')) {
            $this->app->register('\\Subscribo\\TransactionPluginKlarna\\Integration\\Laravel\\TransactionPluginKlarnaServiceProvider');
        }
        if (class_exists('\\Subscribo\\TransactionPluginPayUnity\\Integration\\Laravel\\TransactionPluginPayUnityServiceProvider')) {
            $this->app->register('\\Subscribo\\TransactionPluginPayUnity\\Integration\\Laravel\\TransactionPluginPayUnityServiceProvider');
        }
    }
}
