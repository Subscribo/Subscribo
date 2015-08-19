<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Subscribo\TransactionPluginManager\Interfaces\InterruptionFacadeInterface;
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
     * @param int|string|array|mixed $questionary
     * @param TransactionProcessorInterface $processor
     * @param InterruptionFacadeInterface|null $interruption
     * @return mixed|void
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     */
    public function interruptByQuestionary(
        $questionary,
        TransactionProcessorInterface $processor,
        InterruptionFacadeInterface $interruption = null
    );

    /**
     * @param string|mixed $widget
     * @param TransactionProcessorInterface $processor
     * @param InterruptionFacadeInterface|null $interruption
     * @return mixed|void
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     */
    public function interruptByWidget(
        $widget,
        TransactionProcessorInterface $processor,
        InterruptionFacadeInterface $interruption = null
    );

    /**
     * @param string|mixed $redirection
     * @param TransactionProcessorInterface $processor
     * @param InterruptionFacadeInterface|null $interruption
     * @return mixed|void
     * @throws \Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException
     */
    public function interruptByClientRedirection(
        $redirection,
        TransactionProcessorInterface $processor,
        InterruptionFacadeInterface $interruption = null
    );

    /**
     * @param TransactionProcessorInterface $processor
     * @return InterruptionFacadeInterface
     */
    public function prepareInterruptionFacade(TransactionProcessorInterface $processor);
}
