<?php namespace Subscribo\Api0;

use Response;
use Subscribo\ServiceProvider\ServiceProvider;


/**
 * Class Api0ServiceProvider
 *
 * @package Subscribo\Api0
 */
class Api0ServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function register()
    {
        $this->app->register('\\Subscribo\\Modifier\\Integration\\Laravel\\ModifierServiceProvider');
        $this->app->register('\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider');
        $this->app->alias('subscribo.api0modelcontroller', 'Subscribo\Api0\ModelController');
    }

    public function boot()
    {
        $this->package('subscribo/api0');
        $this->app->make('router')->get('api/v0/model/{model}/{identifier?}', ['middleware' => [], 'uses' => '\\Subscribo\Api0\ModelController@getIndex']);
        $this->app->make('router')->post('api/v0/model/{model}', ['middleware' => [], 'uses' => '\\Subscribo\Api0\ModelController@addElement']);
        $this->app->make('router')->post('api/v0/model/{model}/{identifier?}', ['middleware' => [], 'uses' => '\\Subscribo\Api0\ModelController@modifyElement']);
        $this->app->make('router')->put('api/v0/model/{model}/{identifier}', ['middleware' => [], 'uses' => '\\Subscribo\Api0\ModelController@putElement']);
        $this->app->make('router')->delete('api/v0/model/{model}/{identifier}', ['middleware' => [], 'uses' => '\\Subscribo\Api0\ModelController@deleteElement']);
        $this->app->make('router')->options('api/v0/model/{model}/{identifier?}', ['middleware' => [], function() {
            return Response::make(array("methods" => array("GET", "POST", "PUT", "DELETE", "OPTIONS")), 200, array('Allow' => "GET, POST, PUT, DELETE, OPTIONS"));
        }]);


    }
}
