<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model AclRight
 *
 */

return array(
    'title' => 'Acl rights',
    'single' => 'acl right',
    'model' => '\\Model\\AclRight',
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

        'api_method_id' => array (
            'title' => 'Api method id',
        ),

        'apiMethod' => array (
            'title' => 'Api method',
            'relationship' => 'apiMethod',
            'select' => '(:table).identifier',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'aclRoles' => array (
            'title' => 'Acl roles',
            'relationship' => 'aclRoles',
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

        'apiMethod' => array (
            'title' => 'Api method',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'aclRoles' => array (
            'title' => 'Acl roles',
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

        'api_method_id' => array (
            'title' => 'Api method id',
            'type' => 'number',
        ),
    ),
);
