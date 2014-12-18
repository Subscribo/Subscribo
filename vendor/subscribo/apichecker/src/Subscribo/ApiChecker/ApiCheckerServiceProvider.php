<?php namespace Subscribo\ApiChecker;

use Illuminate\Support\ServiceProvider;

/**
 * Class ApiCheckerServiceProvider
 *
 * @package Subscribo\ApiChecker
 */
class ApiCheckerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\ModelBase\\Support\\Laravel\\ModelBaseServiceProvider');
        $this->app->make('router')->get('checker', function() {
            return $this->app->make('view')->make('apichecker::checker');
        });

    }

    public function boot()
    {
        $this->package('subscribo/apichecker');
    }
}
