<?php namespace Subscribo\Modifier\Support\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Modifier
 *
 * @package Subscribo\Modifier
 */
class Modifier extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'subscribo.modifier';
    }

}
