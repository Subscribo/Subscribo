<?php namespace Subscribo\Localization\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Localization
 *
 * @package Subscribo\Localization
 */
class Localization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'subscribo.localization.manager';
    }
}
