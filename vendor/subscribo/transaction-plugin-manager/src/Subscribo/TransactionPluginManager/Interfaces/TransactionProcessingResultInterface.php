<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Exception;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

/**
 * Interface TransactionProcessingResultInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface TransactionProcessingResultInterface
{
    const STATUS_UNDEFINED = null;
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILURE = 'failure';
    const STATUS_ERROR = 'error';
    const STATUS_WAITING = 'waiting';
    const STATUS_INTERRUPTION = 'interruption';
    const STATUS_SKIPPED = 'skipped';

    const WAITING_FOR_CUSTOMER_INPUT = 'waiting_for_customer_input'; //Necessary? Remove?
    const WAITING_FOR_CUSTOMER_CONFIRMATION = 'waiting_for_customer_confirmation'; //Merge with  WAITING_FOR_CUSTOMER_INPUT? and rename to WAITING_FOR_CUSTOMER?
    const WAITING_FOR_MERCHANT_INPUT = 'waiting_for_merchant_input'; //Necessary? Remove?
    const WAITING_FOR_MERCHANT_CONFIRMATION = 'waiting_for_merchant_confirmation'; //Necessary? Remove?
    const WAITING_FOR_MERCHANT_PROCESSING = 'waiting_for_merchant_processing';
    const WAITING_FOR_MERCHANT_DECISION = 'waiting_for_merchant_decision'; //E.g. whether to accept the risk
    const WAITING_FOR_GATEWAY_PROCESSING = 'waiting_for_gateway_processing';
    const WAITING_FOR_THIRD_PARTY = 'waiting_for_third_party'; //Necessary? Remove?

    const ERROR_INPUT = 'error_input'; //For errors caused by invalid input from Customer (Customer may check and fix)
    const ERROR_CONNECTION = 'error_connection'; //For errors cause after an API call to gateway has been made
    const ERROR_RESPONSE = 'error_response'; //For errors caused by unexpected response of Gateway API
    const ERROR_GATEWAY = 'error_gateway'; //Gateway responded by reasonable manner, but is telling, that it is not able to proceed this transaction at this point
    const ERROR_TRANSACTION = 'error_transaction'; //Errors caused by transaction being in unexpected state, stage or missing required data
    const ERROR_SERVER = 'error_server'; //For errors caused by server logic, database, unknown errors etc.

    const FAILURE_UNSPECIFIED = 'failure_unspecified'; //A catch-all for all kinds of problems (mainly) on Customer side
    const FAILURE_DENIED = 'failure_denied'; //Customer or Card has not passed risk assessment
    const FAILURE_INSUFFICIENT_FUNDS = 'failure_insufficient_funds';
    const FAILURE_LIMIT_EXCEEDED = 'failure_limit_exceeded'; //Daily or Monthly or for other period limit for amount or count of transaction has been exceeded
    const FAILURE_CARD_EXPIRED = 'failure_card_expired';
    const FAILURE_CARD_BLOCKED = 'failure_card_blocked';
    const FAILURE_CARD_NOT_ACTIVATED = 'failure_card_not_activated'; //Card has not been activated for internet payments

    const SKIPPED_PROCESSED = 'skipped_processed'; //Transaction has already been processed
    const SKIPPED_WRONG_STAGE = 'skipped_wrong_stage'; //Transaction is in wrong stage, not allowing start of processing

    /**
     * Uncertainty logic constants for moneyAreReserved() and moneyAreTransferred()
     */
    const UNDEFINED = null; //For states, which have not been defined or managed yet or where not applicable (we do not know, but maybe only because we did not payed attention)
    const YES = 'yes';
    const NO = 'no';
    const POSSIBLY = 'possibly'; //For states, when we are not sure, but we know we are not sure - this should happen only when state is Error or Waiting
    const NON_APPLICABLE = 'non_applicable'; //For states, when we do not care and know, we do not care intentionally (not by unintended omission)

    /**
     * @return TransactionFacadeInterface
     */
    public function getTransactionFacadeObject();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @return bool
     */
    public function isRegistered();

    /**
     * Probability of state, whether money has been actually reserved on account of Customer but not transferred to account of Merchant
     * Money are "in between" and some action on Merchant, Customer or Bank side (or waiting a certain period) is needed
     * in order to get the money through or back.
     *
     * @return string
     */
    public function moneyAreReserved();

    /**
     * Does not actually state, whether money have been physically transferred from one (bank/card) account
     * to the other, but describes probability of situation whether transaction could be considered as proceeded
     * (as defined by type of transaction)
     * There might still be some activity needed on Customer side (paying the invoice),
     * Merchant side (manual processing), bank side or gateway side (money transfer or processing)
     * but these are only typical actions for a particular transaction type
     * and no extra / untypical action is necessary on any side
     *
     * @return string|null
     */
    public function moneyAreTransferred();

    /**
     * @return string|null
     */
    public function getReason();

    /**
     * @return bool
     */
    public function shouldContinue();

    /**
     * @return array
     */
    public function export();

    /**
     * Invalid input fields (as defined in \Subscribo\Omnipay\Shared\CrdietCard) are keys,
     * messages are values(true to get the main or default message)
     * @return array
     */
    public function getInvalidInputFields();

    /**
     * @return Exception|null
     */
    public function getException();
}
