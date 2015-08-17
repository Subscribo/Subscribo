<?php

namespace Subscribo\TransactionPluginManager\Facades;

use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Traits\TransparentFacadeTrait;
use Subscribo\ModelCore\Models\Transaction;

/**
 * Class TransactionFacade
 *
 * @package Subscribo\TransactionPluginManager
 */
class TransactionFacade implements TransactionFacadeInterface
{
    use TransparentFacadeTrait;

    /** @var Transaction  */
    protected $instanceOfObjectBehindFacade;

    /** @var string  */
    protected static $classNameOfObjectBehindFacade = '\\Subscribo\\ModelCore\\Models\\Transaction';

    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->instanceOfObjectBehindFacade = $transaction;
    }

    /**
     * @return Transaction
     */
    public function getTransactionModelInstance()
    {
        return $this->instanceOfObjectBehindFacade;
    }

    /**
     * @return null|array
     */
    public function getAnswerFromQuestionary()
    {
        if (isset($this->instanceOfObjectBehindFacade->processingData['answerFromQuestionary'])) {

            return $this->instanceOfObjectBehindFacade->processingData['answerFromQuestionary'];
        }

        return null;
    }

    /**
     * @return null|array
     */
    public function getAnswerFromWidget()
    {
        if (isset($this->instanceOfObjectBehindFacade->processingData['answerFromWidget'])) {

            return $this->instanceOfObjectBehindFacade->processingData['answerFromWidget'];
        }

        return null;
    }

    /**
     * @return null|array
     */
    public function getAnswerFromClientRedirection()
    {
        if (isset($this->instanceOfObjectBehindFacade->processingData['answerFromClientRedirection'])) {

            return $this->instanceOfObjectBehindFacade->processingData['answerFromClientRedirection'];
        }

        return null;
    }


    /**
     * @return bool
     */
    public function isChargeTransaction()
    {
        return ((Transaction::DIRECTION_RECEIVE === $this->instanceOfObjectBehindFacade->direction)
            and (Transaction::TYPE_STANDARD === $this->instanceOfObjectBehindFacade->type));
    }
}
