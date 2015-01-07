<?php namespace Subscribo\Config\Loader;

class JsonFileLoader extends FileLoader {

    public function load($resource, $type = null)
    {
        if ( ! is_string($resource)) {
            throw new \InvalidArgumentException('Resource have to be string');
        }
        $content = file_get_contents($resource);
        $result = json_decode($content, true);
        return $result;
    }

    public function supports($resource, $type = null)
    {
        if ( ! is_string($resource)) {
            return false;
        }
        return self::compareExtensions($resource, 'json');
    }
}
