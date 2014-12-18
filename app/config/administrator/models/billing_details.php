<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model BillingDetail
 *
 */

return array(
    'title' => 'Billing details',
    'single' => 'billing detail',
    'model' => '\\Model\\BillingDetail',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'type' => array (
            'title' => 'Type',
        ),

        'address_id' => array (
            'title' => 'Address id',
        ),

        'address' => array (
            'title' => 'Address',
            'relationship' => 'address',
            'select' => '(:table).city',
        ),

        'account_no' => array (
            'title' => 'Account no',
        ),

        'bank_id' => array (
            'title' => 'Bank id',
        ),

        'bank' => array (
            'title' => 'Bank',
            'relationship' => 'bank',
            'select' => '(:table).name',
        ),

        'iban' => array (
            'title' => 'Iban',
        ),

        'bic' => array (
            'title' => 'Bic',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'users' => array (
            'title' => 'Users',
            'relationship' => 'users',
            'select' => 'GROUP_CONCAT((:table).username)',
        ),

        'payments' => array (
            'title' => 'Payments',
            'relationship' => 'payments',
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
            'value' => 1,
        ),

        'address' => array (
            'title' => 'Address',
            'type' => 'relationship',
            'name_field' => 'city',
        ),

        'account_no' => array (
            'title' => 'Account no',
            'type' => 'text',
        ),

        'bank' => array (
            'title' => 'Bank',
            'type' => 'relationship',
            'name_field' => 'name',
        ),

        'iban' => array (
            'title' => 'Iban',
            'type' => 'text',
        ),

        'bic' => array (
            'title' => 'Bic',
            'type' => 'text',
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
        ),

        'address_id' => array (
            'title' => 'Address id',
            'type' => 'number',
        ),

        'account_no' => array (
            'title' => 'Account no',
            'type' => 'text',
        ),

        'bank_id' => array (
            'title' => 'Bank id',
            'type' => 'number',
        ),

        'iban' => array (
            'title' => 'Iban',
            'type' => 'text',
        ),

        'bic' => array (
            'title' => 'Bic',
            'type' => 'text',
        ),
    ),
);
