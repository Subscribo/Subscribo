<?php namespace Subscribo\Exception\Interfaces;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExceptionHandlerInterface
 *
 * Interface, which could be used to define handling Exceptions in Subscribo projects made with Laravel
 *
 * @package Subscribo\Exception
 */
interface ExceptionHandlerInterface extends ExceptionHandler {

    /**
     * @param Exception $e
     * @param Request|null $request
     * @return Response|string|null
     */
    public function handle(Exception $e, Request $request = null);
}
