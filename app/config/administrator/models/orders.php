<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Order
 *
 */

return array(
    'title' => 'Orders',
    'single' => 'order',
    'model' => '\\Model\\Order',
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

        'payment_id' => array (
            'title' => 'Payment id',
        ),

        'payment' => array (
            'title' => 'Payment',
            'relationship' => 'payment',
            'select' => '(:table).type',
        ),

        'delivery_id' => array (
            'title' => 'Delivery id',
        ),

        'delivery' => array (
            'title' => 'Delivery',
            'relationship' => 'delivery',
            'select' => '(:table).start',
        ),

        'delivery_window_id' => array (
            'title' => 'Delivery window id',
        ),

        'deliveryWindow' => array (
            'title' => 'Delivery window',
            'relationship' => 'deliveryWindow',
            'select' => '(:table).start',
        ),

        'anticipated_delivery_start' => array (
            'title' => 'Anticipated delivery start',
        ),

        'anticipated_delivery_end' => array (
            'title' => 'Anticipated delivery end',
        ),

        'subscription_id' => array (
            'title' => 'Subscription id',
        ),

        'subscription' => array (
            'title' => 'Subscription',
            'relationship' => 'subscription',
            'select' => '(:table).account_id',
        ),

        'account_id' => array (
            'title' => 'Account id',
        ),

        'account' => array (
            'title' => 'Account',
            'relationship' => 'account',
            'select' => '(:table).user_id',
        ),

        'shipping_address_id' => array (
            'title' => 'Shipping address id',
        ),

        'shippingAddress' => array (
            'title' => 'Shipping address',
            'relationship' => 'shippingAddress',
            'select' => '(:table).city',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'realizationsInOrders' => array (
            'title' => 'Realizations in orders',
            'relationship' => 'realizationsInOrders',
            'select' => 'GROUP_CONCAT((:table).order_id)',
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
            'type' => 'enum',
            'options' => array (
                'automatic',
                'manual',
            ),
        ),

        'status' => array (
            'title' => 'Status',
            'type' => 'number',
            'description' => '1 - ordering / 2 - ordered / 3 - prepared / 4 - send / 5 - delivered / 6 - returned, 7 -  cancelled...)',
        ),

        'payment' => array (
            'title' => 'Payment',
            'type' => 'relationship',
            'name_field' => 'type',
        ),

        'delivery' => array (
            'title' => 'Delivery',
            'type' => 'relationship',
            'name_field' => 'start',
        ),

        'deliveryWindow' => array (
            'title' => 'Delivery window',
            'type' => 'relationship',
            'name_field' => 'start',
        ),

        'anticipated_delivery_start' => array (
            'title' => 'Anticipated delivery start',
            'type' => 'datetime',
        ),

        'anticipated_delivery_end' => array (
            'title' => 'Anticipated delivery end',
            'type' => 'datetime',
        ),

        'subscription' => array (
            'title' => 'Subscription',
            'type' => 'relationship',
            'name_field' => 'account_id',
        ),

        'account' => array (
            'title' => 'Account',
            'type' => 'relationship',
            'name_field' => 'user_id',
        ),

        'shippingAddress' => array (
            'title' => 'Shipping address',
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

        'type' => array (
            'title' => 'Type',
            'type' => 'enum',
            'options' => array (
                'automatic',
                'manual',
            ),
        ),

        'status' => array (
            'title' => 'Status',
            'type' => 'number',
            'description' => '1 - ordering / 2 - ordered / 3 - prepared / 4 - send / 5 - delivered / 6 - returned, 7 -  cancelled...)',
        ),

        'payment_id' => array (
            'title' => 'Payment id',
            'type' => 'number',
        ),

        'delivery_id' => array (
            'title' => 'Delivery id',
            'type' => 'number',
        ),

        'delivery_window_id' => array (
            'title' => 'Delivery window id',
            'type' => 'number',
        ),

        'anticipated_delivery_start' => array (
            'title' => 'Anticipated delivery start',
            'type' => 'datetime',
        ),

        'anticipated_delivery_end' => array (
            'title' => 'Anticipated delivery end',
            'type' => 'datetime',
        ),

        'subscription_id' => array (
            'title' => 'Subscription id',
            'type' => 'number',
        ),

        'account_id' => array (
            'title' => 'Account id',
            'type' => 'number',
        ),

        'shipping_address_id' => array (
            'title' => 'Shipping address id',
            'type' => 'number',
        ),
    ),
);
