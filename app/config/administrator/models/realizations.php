<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Realization
 *
 */

return array(
    'title' => 'Realizations',
    'single' => 'realization',
    'model' => '\\Model\\Realization',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'identifier' => array (
            'title' => 'Identifier',
        ),

        'comment' => array (
            'title' => 'Comment',
        ),

        'product_id' => array (
            'title' => 'Product id',
        ),

        'product' => array (
            'title' => 'Product',
            'relationship' => 'product',
            'select' => '(:table).identifier',
        ),

        'delivery_id' => array (
            'title' => 'Delivery id',
        ),

        'delivery' => array (
            'title' => 'Delivery',
            'relationship' => 'delivery',
            'select' => '(:table).start',
        ),

        'name' => array (
            'title' => 'Name',
        ),

        'description' => array (
            'title' => 'Description',
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

        'kochaboMeasuredRecipesInRealizations' => array (
            'title' => 'Kochabo measured recipes in realizations',
            'relationship' => 'kochaboMeasuredRecipesInRealizations',
            'select' => 'GROUP_CONCAT((:table).realization_id)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'identifier' => array (
            'title' => 'Identifier',
            'type' => 'text',
            'description' => 'unique string used in API',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),

        'product' => array (
            'title' => 'Product',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'delivery' => array (
            'title' => 'Delivery',
            'type' => 'relationship',
            'name_field' => 'start',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),

        'description' => array (
            'title' => 'Description',
            'type' => 'text',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'identifier' => array (
            'title' => 'Identifier',
            'type' => 'text',
            'description' => 'unique string used in API',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),

        'product_id' => array (
            'title' => 'Product id',
            'type' => 'number',
        ),

        'delivery_id' => array (
            'title' => 'Delivery id',
            'type' => 'number',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),

        'description' => array (
            'title' => 'Description',
            'type' => 'text',
        ),
    ),
);
