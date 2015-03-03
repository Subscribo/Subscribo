<?php namespace Subscribo\DependencyResolver;

use Subscribo\DependencyResolver\DependencyResolverInterface;
use Subscribo\DependencyResolver\FlatDependencyResolver;
use Subscribo\DependencyResolver\CircularDependencyException;
use Subscribo\DependencyResolver\ModeNotImplementedException;
use Subscribo\DependencyResolver\KeyNotFoundException;
use Subscribo\DependencyResolver\ExtraDataException;

/**
 * Class DependencyResolver
 *
 * @package Subscribo\DependencyResolver
 */
class DependencyResolver implements DependencyResolverInterface {

    const MODE_DEFAULT  = 'MODE_DEFAULT';
    const MODE_DUMMY    = 'MODE_DUMMY';
    const MODE_FLAT     = 'MODE_FLAT';


    /**
     * Resolving dependencies. Returns only (ordered) array keys.
     *
     * @param array $dependencies Array of arrays, values of the inner arrays refer to keys in main array
     * @param string $mode algorithm to be used
     * @return array containing ordered array keys
     * @throws \Subscribo\DependencyResolver\CircularDependencyException when circular dependency is found
     * @throws \Subscribo\DependencyResolver\ModeNotImplementedException
     */
    public static function resolve(array $dependencies, $mode = self::MODE_DEFAULT)
    {
        $full = static::resolveFull($dependencies, $mode);
        $result = array_keys($full);
        return $result;
    }

    /**
     * Resolves dependencies (using requested algorithm)
     * Returns array of arrays with resolved dependencies (if possible)
     * i.e. ordered in such a fashion, that all values contained in an array are only those values,
     * which has been used previously as keys in the main array
     *
     * @param array $dependencies Array of arrays, values of the inner arrays refer to keys in main array
     * @param string $mode algorithm to be used
     * @return array Array of arrays with resolved dependencies
     * @throws \Subscribo\DependencyResolver\CircularDependencyException when circular dependency is found
     * @throws \Subscribo\DependencyResolver\ModeNotImplementedException when requested mode is not implemented
     */
    public static function resolveFull(array $dependencies, $mode = self::MODE_DEFAULT)
    {
        if (self::MODE_DEFAULT === $mode) {
            $mode = self::MODE_FLAT;
        }
        switch ($mode) {
            case 'MODE_FLAT':
                return FlatDependencyResolver::resolveFull($dependencies);
            case 'MODE_DUMMY':
                return static::_resolveDummy($dependencies);
            default:
                throw new ModeNotImplementedException("Mode '".$mode."' not recognized.");
        }
    }



    /**
     * Does not actually resolve anything, only returns back parameter
     *
     * @param array $dependencies
     * @return array
     */
    private static function _resolveDummy(array $dependencies)
    {
        return $dependencies;
    }

    /**
     * Orders data based on order of values in order array
     *
     * @param array $data
     * @param array $order
     * @param bool $throwExceptionIfKeyNotFound
     * @param bool $throwExceptionIfExtraData
     * @return array
     * @throws \Subscribo\DependencyResolver\KeyNotFoundException may be thrown, if key is not found in data
     * @throws \Subscribo\DependencyResolver\ExtraDataException may be thrown, if data is found, which did not had their counterpart in order parameter
     */
    public static function reorder(array $data, array $order, $throwExceptionIfKeyNotFound = false, $throwExceptionIfExtraData = false) {
        $result = array();
        foreach($order as $orderedKey) {
            if (array_key_exists($orderedKey, $data)) {
                $result[$orderedKey] = $data[$orderedKey];
                unset($data[$orderedKey]);
            } elseif ($throwExceptionIfKeyNotFound) {
                throw new KeyNotFoundException("Key '".$orderedKey."' not found in data.");
            }
        }
        if (empty($data)) {
            return $result;
        }
        if ($throwExceptionIfExtraData) {
            throw new ExtraDataException("Extra data found.");
        }
        foreach ($data as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }
}
