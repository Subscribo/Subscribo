<?php namespace Subscribo\Exception\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ApiExceptionHandler
 *
 * @package Subscribo\Exception
 */
class ApiExceptionHandler extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'Subscribo\\Exception\\Handlers\\ApiExceptionHandler';
    }

}
