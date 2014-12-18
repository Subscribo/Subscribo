<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Product
 *
 */

return array(
    'title' => 'Products',
    'single' => 'product',
    'model' => '\\Model\\Product',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'identifier' => array (
            'title' => 'Identifier',
        ),

        'service_id' => array (
            'title' => 'Service id',
        ),

        'service' => array (
            'title' => 'Service',
            'relationship' => 'service',
            'select' => '(:table).identifier',
        ),

        'standalone' => array (
            'title' => 'Standalone',
        ),

        'name' => array (
            'title' => 'Name',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'realizations' => array (
            'title' => 'Realizations',
            'relationship' => 'realizations',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'productsInSubscriptions' => array (
            'title' => 'Products in subscriptions',
            'relationship' => 'productsInSubscriptions',
            'select' => 'GROUP_CONCAT((:table).subscription_id)',
        ),

        'kochaboMeasuredRecipes' => array (
            'title' => 'Kochabo measured recipes',
            'relationship' => 'kochaboMeasuredRecipes',
            'select' => 'GROUP_CONCAT((:table).recipe_id)',
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

        'service' => array (
            'title' => 'Service',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'standalone' => array (
            'title' => 'Standalone',
            'type' => 'bool',
            'value' => false,
        ),

        'name' => array (
            'title' => 'Name',
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

        'service_id' => array (
            'title' => 'Service id',
            'type' => 'number',
        ),

        'standalone' => array (
            'title' => 'Standalone',
            'type' => 'bool',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),
    ),
);
