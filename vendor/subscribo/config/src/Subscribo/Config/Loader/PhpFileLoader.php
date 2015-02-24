<?php namespace Subscribo\Config\Loader;


class PhpFileLoader extends FileLoader {

    public function load($resource, $type = null)
    {
        if ( ! is_string($resource)) {
            throw new \InvalidArgumentException('Resource have to be string');
        }
        $result = include ($resource);
        return $result;
    }

    public function supports($resource, $type = null)
    {
        if ( ! is_string($resource)) {
            return false;
        }
        return $this->compareExtensions($resource, 'php');
    }
}
