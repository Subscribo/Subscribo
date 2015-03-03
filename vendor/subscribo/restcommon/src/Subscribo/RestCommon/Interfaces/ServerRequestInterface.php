<?php namespace Subscribo\RestCommon\Interfaces;

interface ServerRequestInterface
{
    /**
     * @return array
     */
    public function export();

    /**
     * @param array $data
     * @return $this
     */
    public function import(array $data);

    /**
     * @return string
     */
    public function getType();


}