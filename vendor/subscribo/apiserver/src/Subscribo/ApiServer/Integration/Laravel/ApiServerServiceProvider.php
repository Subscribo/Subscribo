<?php namespace Subscribo\ApiServer\Integration\Laravel;

use Subscribo\Support\ServiceProvider;

/**
 * Class ApiServerServiceProvider
 *
 * @package Subscribo\ApiServer
 */
class ApiServerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\Api1\\Integration\\Laravel\\Api1ServiceProvider');
        $this->app->register('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');
        $this->app->register('\\Subscribo\\ModelCore\\Integration\\Laravel\\ModelCoreServiceProvider');
        $this->app->register('\\Subscribo\\Billing\\Integration\\Laravel\\BillingServiceProvider');
        $this->registerServiceProvider('\\Subscribo\\ApiServerJob\\Integration\\Laravel\\ApiServerJobServiceProvider')
            ->registerScheduleManager();
    }

    public function boot()
    {
      //  $this->scheduleTasks();

    }
}
