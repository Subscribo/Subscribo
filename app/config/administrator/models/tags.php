<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Tag
 *
 */

return array(
    'title' => 'Tags',
    'single' => 'tag',
    'model' => '\\Model\\Tag',
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

        'tag_group_id' => array (
            'title' => 'Tag group id',
        ),

        'tagGroup' => array (
            'title' => 'Tag group',
            'relationship' => 'tagGroup',
            'select' => '(:table).identifier',
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

        'identifier' => array (
            'title' => 'Identifier',
            'type' => 'text',
            'description' => 'unique string used in API',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),

        'tagGroup' => array (
            'title' => 'Tag group',
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

        'tag_group_id' => array (
            'title' => 'Tag group id',
            'type' => 'number',
        ),
    ),
);
