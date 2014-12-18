<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model AclGroup
 *
 */

return array(
    'title' => 'Acl groups',
    'single' => 'acl group',
    'model' => '\\Model\\AclGroup',
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

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'users' => array (
            'title' => 'Users',
            'relationship' => 'users',
            'select' => 'GROUP_CONCAT((:table).username)',
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

        'users' => array (
            'title' => 'Users',
            'type' => 'relationship',
            'name_field' => 'username',
            'ordering' => 'ordering',
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
    ),
);
