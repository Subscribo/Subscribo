<?php namespace Subscribo\RestCommon\Factories;

use Subscribo\RestCommon\Interfaces\ServerRequestInterface;
use Subscribo\RestCommon\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Widget;


class ServerRequestFactory
{
    protected static $typeMap = [
        Questionary::TYPE => 'Subscribo\\RestCommon\\Questionary',
        ClientRedirection::TYPE => 'Subscribo\\RestCommon\\ClientRedirection',
        Widget::TYPE => 'Subscribo\\RestCommon\\Widget',
    ];

    /**
     * @param array $data
     * @return ServerRequestInterface|Questionary
     * @throws \Subscribo\RestCommon\Exceptions\InvalidArgumentException
     */
    public static function make(array $data)
    {
        if (empty($data['type'])) {
            throw new InvalidArgumentException('Type not specified');
        }
        $type = $data['type'];
        if ( ! is_string($type)) {
            throw new InvalidArgumentException('Type should be a string');
        }
        if ( ! isset($data['data'])) {
            throw new InvalidArgumentException('Data not provided');
        }
        $objectData = $data['data'];
        if ( ! is_array($objectData)) {
            throw new InvalidArgumentException('Data should be an array');
        }
        if (empty(static::$typeMap[$type])) {
            throw new InvalidArgumentException(sprintf("Unrecognized type '%s'", $type));
        }

        $className = static::$typeMap[$type];
        /** @var ServerRequestInterface $instance */
        $instance = new $className();
        $instance->import($objectData);
        return $instance;
    }
}
