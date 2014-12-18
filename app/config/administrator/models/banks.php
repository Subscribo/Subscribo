<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Bank
 *
 */

return array(
    'title' => 'Banks',
    'single' => 'bank',
    'model' => '\\Model\\Bank',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'name' => array (
            'title' => 'Name',
        ),

        'bic' => array (
            'title' => 'Bic',
        ),

        'bank_code' => array (
            'title' => 'Bank code',
        ),

        'country_id' => array (
            'title' => 'Country id',
        ),

        'country' => array (
            'title' => 'Country',
            'relationship' => 'country',
            'select' => '(:table).identifier',
        ),

        'address_id' => array (
            'title' => 'Address id',
        ),

        'address' => array (
            'title' => 'Address',
            'relationship' => 'address',
            'select' => '(:table).city',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'billingDetails' => array (
            'title' => 'Billing details',
            'relationship' => 'billingDetails',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),

        'bic' => array (
            'title' => 'Bic',
            'type' => 'text',
        ),

        'bank_code' => array (
            'title' => 'Bank code',
            'type' => 'text',
        ),

        'country' => array (
            'title' => 'Country',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'address' => array (
            'title' => 'Address',
            'type' => 'relationship',
            'name_field' => 'city',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),

        'bic' => array (
            'title' => 'Bic',
            'type' => 'text',
        ),

        'bank_code' => array (
            'title' => 'Bank code',
            'type' => 'text',
        ),

        'country_id' => array (
            'title' => 'Country id',
            'type' => 'number',
        ),

        'address_id' => array (
            'title' => 'Address id',
            'type' => 'number',
            'description' => 'Address of the bank',
        ),
    ),
);
