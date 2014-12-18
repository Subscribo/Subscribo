<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Subscription
 *
 */

return array(
    'title' => 'Subscriptions',
    'single' => 'subscription',
    'model' => '\\Model\\Subscription',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'account_id' => array (
            'title' => 'Account id',
        ),

        'account' => array (
            'title' => 'Account',
            'relationship' => 'account',
            'select' => '(:table).user_id',
        ),

        'delivery_window_type_id' => array (
            'title' => 'Delivery window type id',
        ),

        'deliveryWindowType' => array (
            'title' => 'Delivery window type',
            'relationship' => 'deliveryWindowType',
            'select' => '(:table).day_of_week',
        ),

        'period' => array (
            'title' => 'Period',
        ),

        'status' => array (
            'title' => 'Status',
        ),

        'start' => array (
            'title' => 'Start',
        ),

        'end' => array (
            'title' => 'End',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'vetos' => array (
            'title' => 'Vetos',
            'relationship' => 'vetos',
            'select' => 'GROUP_CONCAT((:table).start)',
        ),

        'productsInSubscriptions' => array (
            'title' => 'Products in subscriptions',
            'relationship' => 'productsInSubscriptions',
            'select' => 'GROUP_CONCAT((:table).subscription_id)',
        ),

        'subscriptionOrders' => array (
            'title' => 'Subscription orders',
            'relationship' => 'subscriptionOrders',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'account' => array (
            'title' => 'Account',
            'type' => 'relationship',
            'name_field' => 'user_id',
        ),

        'deliveryWindowType' => array (
            'title' => 'Delivery window type',
            'type' => 'relationship',
            'name_field' => 'day_of_week',
        ),

        'period' => array (
            'title' => 'Period',
            'type' => 'number',
            'description' => '1 - weekly, 2 - every two weeks, 4 - monthly - other periods could be defined (possibility to use enum, but tiny int used for more flexibility)',
            'value' => 1,
        ),

        'status' => array (
            'title' => 'Status',
            'type' => 'number',
            'description' => '1 - active, 2 - cancelled',
            'value' => 1,
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'date',
        ),

        'end' => array (
            'title' => 'End',
            'type' => 'date',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'account_id' => array (
            'title' => 'Account id',
            'type' => 'number',
        ),

        'delivery_window_type_id' => array (
            'title' => 'Delivery window type id',
            'type' => 'number',
        ),

        'period' => array (
            'title' => 'Period',
            'type' => 'number',
            'description' => '1 - weekly, 2 - every two weeks, 4 - monthly - other periods could be defined (possibility to use enum, but tiny int used for more flexibility)',
        ),

        'status' => array (
            'title' => 'Status',
            'type' => 'number',
            'description' => '1 - active, 2 - cancelled',
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'date',
        ),

        'end' => array (
            'title' => 'End',
            'type' => 'date',
        ),
    ),
);
