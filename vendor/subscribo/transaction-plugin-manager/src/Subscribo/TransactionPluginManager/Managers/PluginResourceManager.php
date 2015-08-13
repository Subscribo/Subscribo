<?php

namespace Subscribo\TransactionPluginManager\Managers;

use InvalidArgumentException;
use Subscribo\TransactionPluginManager\Interfaces\TransactionDriverManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Facades\LocalizerFacade;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PluginResourceManager
 *
 * @package Subscribo\TransactionPluginManager
 */
class PluginResourceManager implements PluginResourceManagerInterface
{
    /** @var TransactionDriverManagerInterface  */
    protected $driverManager;

    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var LocalizerFacade  */
    protected $localizerFacade;

    /** @var LoggerInterface  */
    protected $logger;

    /**
     * @param TransactionDriverManagerInterface $driverManager
     * @param LocalizerInterface $localizer
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransactionDriverManagerInterface $driverManager,
        LocalizerInterface $localizer,
        LoggerInterface $logger
    ) {
        $this->driverManager = $driverManager;
        $this->localizer = $localizer;
        $this->logger = $logger;
        $this->localizerFacade = new LocalizerFacade($localizer);
    }

    /**
     * Returns specified driver, connected to this instance of PluginResourceManagerInterface as its resource manager
     *
     * @param TransactionPluginDriverInterface|string $name
     * @return TransactionPluginDriverInterface
     * @throws InvalidArgumentException
     */
    public function getDriver($name)
    {
        if ($name instanceof TransactionPluginDriverInterface) {
            $driver = $name;
        } else {
            $driver = $this->driverManager->getDriver($name);
        }

        return $driver->withPluginResourceManager($this);
    }

    /**
     * @return LocalizerFacade|LocalizerFacadeInterface
     */
    public function getLocalizer()
    {
        return $this->localizerFacade;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
