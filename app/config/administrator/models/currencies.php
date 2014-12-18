<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Currency
 *
 */

return array(
    'title' => 'Currencies',
    'single' => 'currency',
    'model' => '\\Model\\Currency',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'name' => array (
            'title' => 'Name',
        ),

        'code' => array (
            'title' => 'Code',
        ),

        'symbol' => array (
            'title' => 'Symbol',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
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

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),

        'code' => array (
            'title' => 'Code',
            'type' => 'text',
        ),

        'symbol' => array (
            'title' => 'Symbol',
            'type' => 'text',
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

        'code' => array (
            'title' => 'Code',
            'type' => 'text',
        ),

        'symbol' => array (
            'title' => 'Symbol',
            'type' => 'text',
        ),
    ),
);
