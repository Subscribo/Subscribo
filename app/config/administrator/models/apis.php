<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Api
 *
 */

return array(
    'title' => 'Apis',
    'single' => 'api',
    'model' => '\\Model\\Api',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'identifier' => array (
            'title' => 'Identifier',
        ),

        'name' => array (
            'title' => 'Name',
        ),

        'comment' => array (
            'title' => 'Comment',
        ),

        'version' => array (
            'title' => 'Version',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'services' => array (
            'title' => 'Services',
            'relationship' => 'services',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'apiMethods' => array (
            'title' => 'Api methods',
            'relationship' => 'apiMethods',
            'select' => 'GROUP_CONCAT((:table).identifier)',
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

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
            'description' => 'human readable name',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),

        'version' => array (
            'title' => 'Version',
            'type' => 'number',
            'value' => 1,
        ),

        'services' => array (
            'title' => 'Services',
            'type' => 'relationship',
            'name_field' => 'identifier',
            'ordering' => 'ordering',
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

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
            'description' => 'human readable name',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),

        'version' => array (
            'title' => 'Version',
            'type' => 'number',
        ),
    ),
);
