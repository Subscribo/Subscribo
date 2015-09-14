<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Exception;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\ModelCore\Models\Transaction;

/**
 * Abstract Class TransactionProcessorBase
 *
 * @package Subscribo\TransactionPluginManager
 */
abstract class TransactionProcessorBase implements TransactionProcessorInterface
{

    /** @var TransactionPluginDriverInterface */
    protected $driver;

    /** @var TransactionFacadeInterface */
    protected $transaction;

    /** @var TransactionProcessingResultBase */
    protected $result;

    /**
     * @return TransactionProcessingResultInterface
     */
    abstract protected function doProcess();

    /**
     * @param TransactionPluginDriverInterface $driver
     * @param TransactionFacadeInterface $transaction
     */
    public function __construct(TransactionPluginDriverInterface $driver, TransactionFacadeInterface $transaction)
    {
        $this->driver = $driver;
        $this->transaction = $transaction;
    }

    public function process()
    {
        $this->result = new TransactionProcessingResultBase($this->transaction, null);
        try {
            $result = $this->doProcess();
            if (empty($result)) {
                $result = $this->result;
            }
        } catch (Exception $e) {
            if ($this->shouldLogException()) {
                $this->getLogger()->critical($e);
            }
            $result = $this->result->error(TransactionProcessingResultInterface::ERROR_SERVER);
        }
        if ($this->shouldSupplyMessage() and method_exists($result, 'supplyMessageIfNotPresent')) {
            $result->supplyMessageIfNotPresent($this->getLocalizer());
        }

        return $result;
    }

    /**
     * @return TransactionFacadeInterface
     */
    public function getTransactionFacade()
    {
        return $this->transaction;
    }

    /**
     * @return string
     */
    public function getDriverIdentifier()
    {
        return $this->driver->getDriverIdentifier();
    }

    /**
     * @return \Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface
     */
    protected function getLocalizer()
    {
        return $this->driver->getPluginResourceManager()->getLocalizer();
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return $this->driver->getPluginResourceManager()->getLogger();
    }

    /**
     * @return bool
     */
    protected function shouldLogException()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function shouldSupplyMessage()
    {
        return true;
    }

    /**
     * @return $this
     */
    protected function switchResultMoneyStart()
    {
        $this->result->setMoneyAreReserved(TransactionProcessingResultInterface::NO);
        $this->result->setMoneyAreTransferred(TransactionProcessingResultInterface::NO);

        return $this;
    }

    /**
     * @param bool $already Whether the operation already has proceeded and confirmation has been obtained
     * @return $this
     */
    protected function switchResultMoneyReserved($already)
    {
        $state = $already ? TransactionProcessingResultInterface::YES : TransactionProcessingResultInterface::POSSIBLY;
        $this->result->setMoneyAreReserved($state);

        return $this;
    }

    /**
     * @param bool $already Whether the operation already has proceeded and confirmation has been obtained
     * @return $this
     */
    protected function switchResultMoneyTransferred($already)
    {
        if ($already) {
            $this->result->setMoneyAreTransferred(TransactionProcessingResultInterface::YES);
            $this->result->setMoneyAreReserved(TransactionProcessingResultInterface::NO);
        } else {
            $this->result->setMoneyAreTransferred(TransactionProcessingResultInterface::POSSIBLY);
        }

        return $this;
    }

    /**
     * @param array $allowedStages
     * @param bool $addPlannedStage
     * @return bool
     */
    protected function stageIsNotAmongAllowed($allowedStages = [], $addPlannedStage = true)
    {
        $allowedStages = is_array($allowedStages) ? $allowedStages : [$allowedStages];
        if ($addPlannedStage) {
            $allowedStages[] = Transaction::STAGE_PLANNED;
        }

        $stage = $this->transaction->getTransactionModelInstance()->stage;
        if (in_array($stage, $allowedStages, true)) {
            return false;
        }

        return true;
    }
}
