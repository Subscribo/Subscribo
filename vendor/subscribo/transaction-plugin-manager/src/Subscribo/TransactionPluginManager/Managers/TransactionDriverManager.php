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
     * @param string $name
     * @param Closure|string|TransactionPluginDriverInterface $driver
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function registerDriver($name, $driver)
    {
        if ( ! is_string($name)) {
            throw new InvalidArgumentException('Provided name is not string');
        }
        if (is_string($driver)
            or ($driver instanceof TransactionPluginDriverInterface)
            or ($driver instanceof Closure)
        ) {
            $this->registeredDrivers[$name] = $driver;

            return $this;
        }

        throw new InvalidArgumentException(
            'Provided driver is neither string, nor Closure, nor instance of TransactionPluginDriverInterface'
        );
    }

    /**
     * @param string $name
     * @return TransactionPluginDriverInterface
     * @throws \InvalidArgumentException
     */
    public function getDriver($name)
    {
        if (empty($this->registeredDrivers[$name])) {
            throw new InvalidArgumentException('Requested driver is not registered');
        }
        $driver = $this->registeredDrivers[$name];
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
        $this->registeredDrivers[$name] = $driver;

        return $driver;
    }
}
