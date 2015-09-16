<?php

namespace Subscribo\TransactionPluginManager\Managers;

use Closure;
use InvalidArgumentException;
use Subscribo\TransactionPluginManager\Interfaces\TransactionDriverManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Illuminate\Foundation\Application;

/**
 * Class TransactionDriverManager
 *
 * @package Subscribo\TransactionPluginManager
 */
class TransactionDriverManager implements TransactionDriverManagerInterface
{
    /** @var array  */
    protected $registeredDrivers = [];

    /** @var \Illuminate\Foundation\Application  */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $identifier
     * @param Closure|string|TransactionPluginDriverInterface $driver
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function registerDriver($identifier, $driver)
    {
        if ( ! is_string($identifier)) {
            throw new InvalidArgumentException('Provided name is not string');
        }
        if (is_string($driver)
            or ($driver instanceof TransactionPluginDriverInterface)
            or ($driver instanceof Closure)
        ) {
            $this->registeredDrivers[$identifier] = $driver;

            return $this;
        }

        throw new InvalidArgumentException(
            'Provided driver is neither string, nor Closure, nor instance of TransactionPluginDriverInterface'
        );
    }

    /**
     * @param string $identifier
     * @return TransactionPluginDriverInterface
     * @throws \InvalidArgumentException
     */
    public function getDriver($identifier)
    {
        if (empty($this->registeredDrivers[$identifier])) {
            throw new InvalidArgumentException('Requested driver is not registered');
        }
        $driver = $this->registeredDrivers[$identifier];
        if ($driver instanceof Closure) {
            $driver = call_user_func($driver);
        }
        if (is_string($driver)) {
            $driverObject = $this->app->make($driver);
            if (empty($driverObject)) {
                throw new InvalidArgumentException('Failure to instantiate requested driver');
            }
            $driver = $driverObject;
        }
        if ( ! ($driver instanceof TransactionPluginDriverInterface)) {

            throw new InvalidArgumentException('Driver not instance of TransactionPluginDriverInterface');
        }
        $this->registeredDrivers[$identifier] = $driver;

        return $driver;
    }
}
