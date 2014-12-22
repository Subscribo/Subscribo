<?php namespace Subscribo\Environment\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Environment
 *
 * @package Subscribo\Environment
 */
class Environment extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.environment';
    }

}
