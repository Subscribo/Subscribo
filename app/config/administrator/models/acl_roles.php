<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model AclRole
 *
 */

return array(
    'title' => 'Acl roles',
    'single' => 'acl role',
    'model' => '\\Model\\AclRole',
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

        'aclRights' => array (
            'title' => 'Acl rights',
            'relationship' => 'aclRights',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'aclGroups' => array (
            'title' => 'Acl groups',
            'relationship' => 'aclGroups',
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

        'aclRights' => array (
            'title' => 'Acl rights',
            'type' => 'relationship',
            'name_field' => 'identifier',
            'ordering' => 'ordering',
        ),

        'aclGroups' => array (
            'title' => 'Acl groups',
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
