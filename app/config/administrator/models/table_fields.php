<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model TableField
 *
 */

return array(
    'title' => 'Table fields',
    'single' => 'table field',
    'model' => '\\Model\\TableField',
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

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'translations' => array (
            'title' => 'Translations',
            'relationship' => 'translations',
            'select' => 'GROUP_CONCAT((:table).text)',
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
            'description' => 'unique string used in API - actual field name',
        ),

        'comment' => array (
            'title' => 'Comment',
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
            'description' => 'unique string used in API - actual field name',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),
    ),
);
