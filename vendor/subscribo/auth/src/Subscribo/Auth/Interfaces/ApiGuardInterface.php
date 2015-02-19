<?php namespace Subscribo\Auth\Interfaces;

use Subscribo\Auth\Interfaces\StatelessGuardInterface;
use Symfony\Component\HttpFoundation\Request;


interface ApiGuardInterface extends StatelessGuardInterface
{
    /**
     * @param Request $request
     * @return Request|null
     */
    public function processRequest(Request $request);

    /**
     * @return array|null
     */
    public function processingResult();
}
