<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model ProductsInSubscription
 *
 */

return array(
    'title' => 'Products in subscriptions',
    'single' => 'products in subscription',
    'model' => '\\Model\\ProductsInSubscription',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'subscription_id' => array (
            'title' => 'Subscription id',
        ),

        'subscription' => array (
            'title' => 'Subscription',
            'relationship' => 'subscription',
            'select' => '(:table).account_id',
        ),

        'product_id' => array (
            'title' => 'Product id',
        ),

        'product' => array (
            'title' => 'Product',
            'relationship' => 'product',
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

        'subscription' => array (
            'title' => 'Subscription',
            'type' => 'relationship',
            'name_field' => 'account_id',
        ),

        'product' => array (
            'title' => 'Product',
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

        'subscription_id' => array (
            'title' => 'Subscription id',
            'type' => 'number',
        ),

        'product_id' => array (
            'title' => 'Product id',
            'type' => 'number',
        ),

        'amount' => array (
            'title' => 'Amount',
            'type' => 'number',
        ),
    ),
);
