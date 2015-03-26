<?php namespace Subscribo\Localization\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Localizer
 *
 * @package Subscribo\Localization
 */
class Localizer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'subscribo.localizer';
    }
}
