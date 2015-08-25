<?php

namespace Subscribo\TransactionPluginManager\Bases;

use Exception;
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
    public $transactionFacadeObject;

    /**
     * @var string
     */
    public $status;

    /**
     * @var null|string
     */
    public $message;

    /**
     * @var bool
     */
    public $registered;

    /**
     * @var string|null
     */
    public $reason;

    /**
     * @var Exception|null
     */
    public $exception;

    /**
     * @var array
     */
    public $invalidInputFields = [];

    /**
     * @var string|null
     */
    public $moneyReserved = TransactionProcessingResultInterface::UNDEFINED;

    /**
     * @var string|null
     */
    public $moneyTransferred = TransactionProcessingResultInterface::UNDEFINED;

    /**
     * @param TransactionFacadeInterface $transaction
     * @param string $status
     * @param string|null $message
     * @param bool $registered
     * @param string|null $reason
     */
    public function __construct(TransactionFacadeInterface $transaction, $status, $message = null, $registered = false, $reason = null)
    {
        $this->transactionFacadeObject = $transaction;
        $this->status = $status;
        $this->message = $message;
        $this->registered = $registered;
        $this->reason = $reason;
    }

    /**
     * @param TransactionFacadeInterface $transaction
     * @param Exception $exception
     * @return TransactionProcessingResultBase
     */
    public static function makeInterruptionResult(TransactionFacadeInterface $transaction, Exception $exception)
    {
        $instance = new static($transaction, static::STATUS_INTERRUPTION);
        $instance->setException($exception);

        return $instance;
    }

    public function getGenericMessage(LocalizerFacadeInterface $localizer)
    {
        $loc = $localizer->getLocalizerInstance();
        $status = $this->getStatus() ?: static::STATUS_ERROR;
        $reason = $this->getReason();
        $fallbackId = 'messages.fallback.'.$status;
        $domain = 'transaction-plugin-manager::processingresult';
        $fallbackMessage = $loc->transOrDefault($fallbackId, [], $domain, null, '???');
        if ($reason) {
            $id = 'messages.specific.'.$status.'.'.$reason;
            $message = $loc->transOrDefault($id, [], $domain, null, $fallbackMessage);
        } else {
            $message = $fallbackMessage;
        }
        /* Message add for money reserved / transferred state */
        if ((static::STATUS_ERROR === $status)) {
            $transferred = $this->moneyAreTransferred();
            $reserved = $this->moneyAreReserved();
            if (static::YES === $transferred) {
                $addId = 'messages.add.transferred';
            } elseif (static::POSSIBLY === $transferred) {
                $addId = 'messages.add.possibly_transferred';
            } elseif (static::NO === $transferred) {
                if (static::YES === $reserved) {
                    $addId = 'messages.add.reserved';
                } elseif (static::POSSIBLY === $reserved) {
                    $addId = 'messages.add.possibly_reserved';
                } else {
                    $addId = null;
                }
            } else {
                $addId = 'messages.add.undefined';
            }
            if ($addId) {
                $addToMessage = $loc->transOrDefault($addId, [], $domain, null, null);
                $message = $addToMessage ? ($message.' '.$addToMessage) : $message;
            }
        }

        return $message;
    }

    public function supplyMessageIfNotPresent(LocalizerFacadeInterface $localizer)
    {
        if ( ! $this->getMessage()) {
            $this->setMessage($this->getGenericMessage($localizer));
        }

        return $this;
    }

    /**
     * @return \Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface
     */
    public function getTransactionFacadeObject()
    {
        return $this->transactionFacadeObject;
    }

    /**
     * @param TransactionFacadeInterface $instance
     * @return $this
     */
    public function setTransactionFacadeObject(TransactionFacadeInterface $instance)
    {
        $this->transactionFacadeObject = $instance;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * @param bool $registered
     * @return $this
     */
    public function setIsRegistered($registered = true)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return $this
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return null|string
     */
    public function moneyAreTransferred()
    {
        return $this->moneyTransferred;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setMoneyAreTransferred($state)
    {
        $this->moneyTransferred = $state;

        return $this;
    }

    /**
     * @return null|string
     */
    public function moneyAreReserved()
    {
        return $this->moneyReserved;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setMoneyAreReserved($state)
    {
        $this->moneyReserved = $state;

        return $this;
    }

    /**
     * @return array
     */
    public function getInvalidInputFields()
    {
        return $this->invalidInputFields;
    }

    /**
     * @param array $invalidInputFields
     * @return $this
     */
    public function setInvalidInputFields(array $invalidInputFields)
    {
        $this->invalidInputFields = $invalidInputFields;

        return $this;
    }

    /**
     * @return Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param Exception|null $exception
     * @return $this
     */
    public function setException(Exception $exception = null)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldContinue()
    {
        if (static::STATUS_SUCCESS === $this->getStatus()) {

            return true;
        }
        if (static::STATUS_WAITING === $this->getStatus()) {
            switch(strval($this->getReason())) {
                case static::WAITING_FOR_GATEWAY_PROCESSING:

                    return true;
                case static::WAITING_FOR_CUSTOMER_INPUT:
                case static::WAITING_FOR_CUSTOMER_CONFIRMATION:
                case static::WAITING_FOR_MERCHANT_DECISION:
                case static::WAITING_FOR_MERCHANT_CONFIRMATION:
                case static::WAITING_FOR_MERCHANT_PROCESSING:
                case static::WAITING_FOR_MERCHANT_INPUT:
                case static::WAITING_FOR_THIRD_PARTY:

                    return false;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function export()
    {
        $result = [
            'transaction' => $this->getTransactionFacadeObject()->getTransactionModelInstance(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'registered' => $this->isRegistered(),
            'continue' => $this->shouldContinue(),
            'reason' => $this->getReason(),
            'moneyAreTransferred' => $this->moneyAreTransferred(),
            'moneyAreReserved' => $this->moneyAreTransferred(),
            'invalidInputFields' => $this->getInvalidInputFields(),
        ];

        return $result;
    }

    /**
     * @param string $reason
     * @param string|null $message
     * @return $this
     */
    public function error($reason, $message = null)
    {
        $this->setStatus(static::STATUS_ERROR);
        $this->setReason($reason);
        $this->setMessage($message);

        return $this;
    }

    /**
     * @param array $invalidInputFields
     * @param string|null $message
     * @return $this
     */
    public function invalidInputError(array $invalidInputFields = [], $message = null)
    {
        $this->setStatus(static::STATUS_ERROR);
        $this->setReason(static::ERROR_INPUT);
        $this->setInvalidInputFields($invalidInputFields);
        $this->setMessage($message);

        return $this;
    }
}
