<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model RealizationsInOrder
 *
 */

return array(
    'title' => 'Realizations in orders',
    'single' => 'realizations in order',
    'model' => '\\Model\\RealizationsInOrder',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'order_id' => array (
            'title' => 'Order id',
        ),

        'realizationsInOrdersOrder' => array (
            'title' => 'Realizations in orders order',
            'relationship' => 'realizationsInOrdersOrder',
            'select' => '(:table).type',
        ),

        'realization_id' => array (
            'title' => 'Realization id',
        ),

        'realization' => array (
            'title' => 'Realization',
            'relationship' => 'realization',
            'select' => '(:table).identifier',
        ),

        'amount' => array (
            'title' => 'Amount',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'realizationsInOrdersOrder' => array (
            'title' => 'Realizations in orders order',
            'type' => 'relationship',
            'name_field' => 'type',
        ),

        'realization' => array (
            'title' => 'Realization',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'amount' => array (
            'title' => 'Amount',
            'type' => 'number',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'order_id' => array (
            'title' => 'Order id',
            'type' => 'number',
        ),

        'realization_id' => array (
            'title' => 'Realization id',
            'type' => 'number',
        ),

        'amount' => array (
            'title' => 'Amount',
            'type' => 'number',
        ),
    ),
);
