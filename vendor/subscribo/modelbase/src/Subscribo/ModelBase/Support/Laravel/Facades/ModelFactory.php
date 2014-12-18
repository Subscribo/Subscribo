<?php namespace Subscribo\ModelBase\Support\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ModelFactory
 *
 * @package Subscribo\ModelBase
 */
class ModelFactory extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.modelfactory';
    }
}
