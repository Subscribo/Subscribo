<?php namespace Subscribo\RestClient\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class RestClient
 *
 * @package Subscribo\RestClient
 */
class RestClient extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.restclient';
    }

}
