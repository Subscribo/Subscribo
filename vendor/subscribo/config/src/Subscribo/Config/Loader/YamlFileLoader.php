<?php namespace Subscribo\Config\Loader;

use Symfony\Component\Yaml\Parser;

class YamlFileLoader extends FileLoader {

    public function load($resource, $type = null)
    {
        if ( ! is_string($resource)) {
            return new \InvalidArgumentException('Resource have to be string');
        }
        $content = file_get_contents($resource);
        $parser = new Parser();
        $result = $parser->parse($content);
        return $result;
    }

    public function supports($resource, $type = null)
    {
        if ( ! is_string($resource)) {
            return false;
        }
        return $this->compareExtensions($resource, array('yml', 'yaml'));
    }
}
