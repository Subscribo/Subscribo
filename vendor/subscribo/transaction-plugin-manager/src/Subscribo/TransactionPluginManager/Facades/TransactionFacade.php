<?php

namespace Subscribo\TransactionPluginManager\Facades;

use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Traits\TransparentFacadeTrait;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\Support\Arr;
use Subscribo\ModelCore\Models\AccountTransactionGatewayToken;

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
     * @param null|string $key
     * @return null|array|mixed
     */
    public function getDataToRemember($key = null)
    {
        if ( ! isset($this->instanceOfObjectBehindFacade->processingData['dataToRemember'])) {

            return null;
        }
        $dataToRemember = $this->instanceOfObjectBehindFacade->processingData['dataToRemember'];

        return Arr::get($dataToRemember, $key);
    }

    /**
     * @param mixed $value
     * @param null|string $key
     * @return $this
     */
    public function setDataToRemember($value, $key = null)
    {
        $processingData = $this->instanceOfObjectBehindFacade->processingData;
        $processingData = is_array($processingData) ? $processingData : [];
        $dataToRemember = empty($processingData['dataToRemember']) ? [] : $processingData['dataToRemember'];
        $dataToRemember = is_array($dataToRemember) ? $dataToRemember : [];
        Arr::set($dataToRemember, $key, $value);
        $processingData['dataToRemember'] = $dataToRemember;
        $this->instanceOfObjectBehindFacade->processingData = $processingData;
        $this->instanceOfObjectBehindFacade->save();

        return $this;
    }

    /**
     * @return bool
     */
    public function isChargeTransaction()
    {
        return ((Transaction::DIRECTION_RECEIVE === $this->instanceOfObjectBehindFacade->direction)
            and (Transaction::TYPE_STANDARD === $this->instanceOfObjectBehindFacade->type));
    }

    /**
     * @param string $registrationToken
     * @return bool
     */
    public function rememberRegistrationToken($registrationToken)
    {
        if (empty($registrationToken)) {

            return false;
        }
        $account = $this->instanceOfObjectBehindFacade->account;
        if (empty($account)) {

            return false;
        }
        $configuration = $this->instanceOfObjectBehindFacade->transactionGatewayConfiguration;
        $addedToken = AccountTransactionGatewayToken::addToken($configuration, $account, $registrationToken);

        return (boolean) $addedToken;
    }

    /**
     * @return string|null
     */
    public function retrieveRegistrationToken()
    {
        $account = $this->instanceOfObjectBehindFacade->account;
        if (empty($account)) {

            return null;
        }
        $configuration = $this->instanceOfObjectBehindFacade->transactionGatewayConfiguration;
        $accountTransactionGatewayToken = AccountTransactionGatewayToken::findDefaultOrLast($configuration, $account);

        if (empty($accountTransactionGatewayToken)) {

            return null;
        }

        return $accountTransactionGatewayToken->token;
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function getGatewayConfiguration($key = null)
    {
        $configuration = $this->instanceOfObjectBehindFacade->transactionGatewayConfiguration->configuration ?: [];

        return Arr::get($configuration, $key);
    }

}
