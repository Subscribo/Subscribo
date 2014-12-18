<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model TagGroup
 *
 */

return array(
    'title' => 'Tag groups',
    'single' => 'tag group',
    'model' => '\\Model\\TagGroup',
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

        'tags' => array (
            'title' => 'Tags',
            'relationship' => 'tags',
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
        ),

        'service_id' => array (
            'title' => 'Service id',
            'type' => 'number',
        ),
    ),
);
