<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface PluginResourceManagerInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface PluginResourceManagerInterface
{
    /**
     * Returns specified driver, connected to this instance of PluginResourceManagerInterface as its resource manager
     *
     * @param TransactionPluginDriverInterface|string $driver
     * @return TransactionPluginDriverInterface
     */
    public function getDriver($driver);

    /**
     * @return LocalizerFacadeInterface
     */
    public function getLocalizer();

    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @param string|int|mixed $questionary
     * @param TransactionProcessorInterface $processor
     * @return mixed|void
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     */
    public function interruptByQuestionary($questionary, TransactionProcessorInterface $processor);

    /**
     * @param string|mixed $widget
     * @param TransactionProcessorInterface $processor
     * @return mixed|void
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     */
    public function interruptByWidget($widget, TransactionProcessorInterface $processor);

    /**
     * @param string|mixed $redirection
     * @param TransactionProcessorInterface $processor
     * @return mixed|void
     * @throws \Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException
     */
    public function interruptByClientRedirection($redirection, TransactionProcessorInterface $processor);
}
