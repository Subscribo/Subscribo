<?php
/**
 * English language resource file for TransactionProcessorBase
 */
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface as ProcessingResult;

return [
    'messages' => [
        'generic' => [
            ProcessingResult::STATUS_SUCCESS => 'Thank you for your order',
            ProcessingResult::STATUS_WAITING => 'Transaction processing is waiting',
            ProcessingResult::STATUS_OWN_RISK => 'Please contact our support',
            ProcessingResult::STATUS_ERROR => 'Some error has happened during transaction processing',
            ProcessingResult::STATUS_FAILURE => 'Transaction processing failed',
        ],
    ],
];
