<?php
/**
 * English language resource file for TransactionProcessingResultBase
 */
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface as ProcessingResult;

return [
    'messages' => [
        'fallback' => [
            ProcessingResult::STATUS_SUCCESS => 'Thank you for your order',
            ProcessingResult::STATUS_WAITING => 'Transaction processing is waiting',
            ProcessingResult::STATUS_ERROR => 'Some error has happened during transaction processing',
            ProcessingResult::STATUS_FAILURE => 'Transaction processing failed',
        ],
        'specific' => [
            ProcessingResult::STATUS_WAITING => [
                ProcessingResult::WAITING_FOR_CUSTOMER_INPUT => 'Please, finish your input',
                ProcessingResult::WAITING_FOR_CUSTOMER_CONFIRMATION => 'Please, confirm transaction (check your email if necessary)',
                ProcessingResult::WAITING_FOR_MERCHANT_INPUT => 'You might need to wait until the transaction will be fully processed',
                ProcessingResult::WAITING_FOR_MERCHANT_CONFIRMATION => 'You might need to wait until the transaction will be fully processed',
                ProcessingResult::WAITING_FOR_MERCHANT_PROCESSING => 'You might need to wait until the transaction will be fully processed',
                ProcessingResult::WAITING_FOR_MERCHANT_DECISION => 'You might need to wait until the transaction will be fully processed',
                ProcessingResult::WAITING_FOR_GATEWAY_PROCESSING => 'You might need to wait until the transaction will be fully processed',
                ProcessingResult::WAITING_FOR_THIRD_PARTY => 'You might need to wait until the transaction will be fully processed',
            ],
            ProcessingResult::STATUS_FAILURE => [
                ProcessingResult::FAILURE_UNSPECIFIED =>  'Transaction processing failed',
                ProcessingResult::FAILURE_DENIED => 'Please try other payment method',
                ProcessingResult::FAILURE_INSUFFICIENT_FUNDS => 'Your card / account does not have sufficient funds for this transaction',
                ProcessingResult::FAILURE_LIMIT_EXCEEDED => 'Your card / account does not have sufficient limit for this transaction',
                ProcessingResult::FAILURE_CARD_BLOCKED => 'Your card is blocked',
                ProcessingResult::FAILURE_CARD_EXPIRED => 'Your card is expired',
                ProcessingResult::FAILURE_CARD_NOT_ACTIVATED => 'Your card is not activated for payments on internet',
            ],
            ProcessingResult::STATUS_ERROR => [
                ProcessingResult::ERROR_INPUT => 'Please, check and fix your input.',
                ProcessingResult::ERROR_CONNECTION => 'There was an error when trying to connect with payment gateway. Please, choose other payment option or try again later.',
                ProcessingResult::ERROR_RESPONSE => 'There was an error when trying to connect with payment gateway. Please, choose other payment option or try again later.',
                ProcessingResult::ERROR_GATEWAY => 'Payment gateway is not able to handle your request at the moment. Please, choose other payment option or try again later.',
                ProcessingResult::ERROR_SERVER => 'Some error has happened, when we tried to process your request. Please, choose other payment option or try again later.',
            ],
        ],
        'add' => [
            'transferred' => 'Please, contact our support in order to refund / cancel the transaction or invoice.',
            'possibly_transferred' => 'Please, contact our support in order to check the state of the transaction and for refund / cancelling if needed.',
            'reserved' => 'Please, contact our support in order to cancel the reservation of your money.',
            'possibly_reserved' => 'Please, contact our support in order to cancel the reservation of your money if needed.',
            'undefined' => 'Transaction might have proceeded. Please, contact our support in order to to check the state of the transaction.',
        ]
    ],
];
