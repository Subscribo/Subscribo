<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Account
 *
 */

return array(
    'title' => 'Accounts',
    'single' => 'account',
    'model' => '\\Model\\Account',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'user_id' => array (
            'title' => 'User id',
        ),

        'user' => array (
            'title' => 'User',
            'relationship' => 'user',
            'select' => '(:table).username',
        ),

        'service_id' => array (
            'title' => 'Service id',
        ),

        'service' => array (
            'title' => 'Service',
            'relationship' => 'service',
            'select' => '(:table).identifier',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'subscriptions' => array (
            'title' => 'Subscriptions',
            'relationship' => 'subscriptions',
            'select' => 'GROUP_CONCAT((:table).account_id)',
        ),

        'accountOrders' => array (
            'title' => 'Account orders',
            'relationship' => 'accountOrders',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
        ),

        'user' => array (
            'title' => 'User',
            'type' => 'relationship',
            'name_field' => 'username',
        ),

        'service' => array (
            'title' => 'Service',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
        ),

        'user_id' => array (
            'title' => 'User id',
            'type' => 'number',
        ),

        'service_id' => array (
            'title' => 'Service id',
            'type' => 'number',
        ),
    ),
);
