<?php namespace Subscribo\Config\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * Class Config - Facade for Subscribo Config
 *
 * @package Subscribo\Config
 */
class Config extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.config';
    }
}
