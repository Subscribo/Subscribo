<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Payment
 *
 */

return array(
    'title' => 'Payments',
    'single' => 'payment',
    'model' => '\\Model\\Payment',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'type' => array (
            'title' => 'Type',
        ),

        'status' => array (
            'title' => 'Status',
        ),

        'amount' => array (
            'title' => 'Amount',
        ),

        'vat' => array (
            'title' => 'Vat',
        ),

        'currency_id' => array (
            'title' => 'Currency id',
        ),

        'currency' => array (
            'title' => 'Currency',
            'relationship' => 'currency',
            'select' => '(:table).name',
        ),

        'billing_detail_id' => array (
            'title' => 'Billing detail id',
        ),

        'billingDetail' => array (
            'title' => 'Billing detail',
            'relationship' => 'billingDetail',
            'select' => '(:table).type',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'paymentOrders' => array (
            'title' => 'Payment orders',
            'relationship' => 'paymentOrders',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'type' => array (
            'title' => 'Type',
            'type' => 'number',
            'description' => '1 - send_from_account / 2 - credit_card / 3 - direct_debit ...)',
        ),

        'status' => array (
            'title' => 'Status',
            'type' => 'number',
            'description' => '1 - planned / 2 - requested / 3 - paid / 4 - rejected / 5 - canceled / 6 - requested_back …)',
        ),

        'amount' => array (
            'title' => 'Amount',
            'type' => 'number',
            'decimals' => 2,
        ),

        'vat' => array (
            'title' => 'Vat',
            'type' => 'number',
            'decimals' => 2,
        ),

        'currency' => array (
            'title' => 'Currency',
            'type' => 'relationship',
            'name_field' => 'name',
        ),

        'billingDetail' => array (
            'title' => 'Billing detail',
            'type' => 'relationship',
            'name_field' => 'type',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'type' => array (
            'title' => 'Type',
            'type' => 'number',
            'description' => '1 - send_from_account / 2 - credit_card / 3 - direct_debit ...)',
        ),

        'status' => array (
            'title' => 'Status',
            'type' => 'number',
            'description' => '1 - planned / 2 - requested / 3 - paid / 4 - rejected / 5 - canceled / 6 - requested_back …)',
        ),

        'amount' => array (
            'title' => 'Amount',
            'type' => 'number',
            'decimals' => 2,
        ),

        'vat' => array (
            'title' => 'Vat',
            'type' => 'number',
            'decimals' => 2,
        ),

        'currency_id' => array (
            'title' => 'Currency id',
            'type' => 'number',
        ),

        'billing_detail_id' => array (
            'title' => 'Billing detail id',
            'type' => 'number',
        ),
    ),
);
