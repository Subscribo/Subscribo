<?php namespace Subscribo\Api1\Interfaces;

use Subscribo\Api1\Interfaces\ControllerRegistrarInterface;

interface SelfRegisteringControllerInterface
{

    public static function registerSelf(ControllerRegistrarInterface $router);

}
