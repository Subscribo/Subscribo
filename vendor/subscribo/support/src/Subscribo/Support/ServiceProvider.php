<?php namespace Subscribo\Support;

use Subscribo\Support\Traits\EnhancedServiceProviderTrait;

/**
 * Class ServiceProvider
 * Extending Laravel Framework class fixing and providing some additional functionality
 *
 * @package Subscribo\Support
 */
abstract class ServiceProvider extends \Subscribo\Support\Fixes\ServiceProvider
{
    use EnhancedServiceProviderTrait;

}
