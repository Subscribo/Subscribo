<?php namespace Subscribo\Api1\Interfaces;


interface ControllerRegistrarInterface
{
    /**
     * True for any
     * @return array|bool
     */
    public static function getAcceptedVerbs();

    /**
     * @param string|bool $verb True for any
     * @param string $uri
     * @param string|\Closure|array $action
     * @return void
     */
    public function registerRoute($verb, $uri, $action);

    /**
     * @param string $uri
     * @param array $description
     * @return void
     */
    public function registerDescription($uri, $description);

}
