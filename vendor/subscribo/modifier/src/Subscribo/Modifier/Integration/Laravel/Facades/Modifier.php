<?php namespace Subscribo\Modifier\Integration\Laravel\Facades;

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
