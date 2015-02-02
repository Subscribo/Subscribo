<?php namespace Subscribo\Api1\Interfaces;


interface ControllerRegistrarInterface
{

    /**
     * @param string $verb
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
