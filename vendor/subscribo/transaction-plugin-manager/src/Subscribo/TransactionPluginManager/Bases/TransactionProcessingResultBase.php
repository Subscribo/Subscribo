<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;

/**
 * Class TransactionProcessingResultBase
 *
 * @package Subscribo\TransactionPluginManager
 */
class TransactionProcessingResultBase implements TransactionProcessingResultInterface
{
    /**
     * @var \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface
     */
    protected $transactionFacadeObject;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var null|string
     */
    protected $message;

    /**
     * @var bool
     */
    protected $registered;

    /**
     * @param TransactionFacadeInterface $transaction
     * @param string $status
     * @param string|null $message
     * @param bool $registered
     */
    public function __construct(TransactionFacadeInterface $transaction, $status, $message = null, $registered = false)
    {
        $this->transactionFacadeObject = $transaction;
        $this->status = $status;
        $this->message = $message;
        $this->registered = $registered;
    }

    /**
     * @param string $status
     * @param LocalizerFacadeInterface $localizer
     * @return string
     */
    public static function makeGenericMessage($status, LocalizerFacadeInterface $localizer)
    {
        $id = 'messages.generic.'.$status;
        $domain = 'transaction-plugin-manager::processingresult';

        return $localizer->getLocalizerInstance()->transOrDefault($id, [], $domain, null, '???');
    }


    /**
     * @return \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface
     */
    public function getTransactionFacadeObject()
    {
        return $this->transactionFacadeObject;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * @return bool
     */
    public function shouldContinue()
    {
        switch ($this->getStatus()) {
            case static::STATUS_SUCCESS:
            case static::STATUS_WAITING:
                return true;
            case static::STATUS_OWN_RISK:
            case static::STATUS_FAILURE:
            case static::STATUS_ERROR:
            default:
                return false;
        }
    }

    /**
     * @return array
     */
    public function export()
    {
        return [
            'transaction' => $this->getTransactionFacadeObject()->getTransactionModelInstance(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'registered' => $this->isRegistered(),
            'continue' => $this->shouldContinue(),
        ];
    }
}
