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
     * @return bool
     */
    public function isChargeTransaction()
    {
        return ((Transaction::DIRECTION_RECEIVE === $this->instanceOfObjectBehindFacade->direction)
            and (Transaction::TYPE_STANDARD === $this->instanceOfObjectBehindFacade->type));
    }
}
