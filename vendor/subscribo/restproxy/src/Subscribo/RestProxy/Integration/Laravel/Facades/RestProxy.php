<?php namespace Subscribo\RestProxy\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class RestProxy
 *
 * @package Subscribo\RestProxy
 */
class RestProxy extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.restproxy';
    }

}
