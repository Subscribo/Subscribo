<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model ApiMethod
 *
 */

return array(
    'title' => 'Api methods',
    'single' => 'api method',
    'model' => '\\Model\\ApiMethod',
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

        'api_id' => array (
            'title' => 'Api id',
        ),

        'api' => array (
            'title' => 'Api',
            'relationship' => 'api',
            'select' => '(:table).identifier',
        ),

        'element' => array (
            'title' => 'Element',
        ),

        'http_verb' => array (
            'title' => 'Http verb',
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
            'description' => 'some description, what is it doing',
        ),

        'api' => array (
            'title' => 'Api',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'element' => array (
            'title' => 'Element',
            'type' => 'bool',
            'description' => 'true for element, false for collection',
            'value' => false,
        ),

        'http_verb' => array (
            'title' => 'Http verb',
            'type' => 'enum',
            'options' => array (
                'GET',
                'POST',
                'PUT',
                'DELETE',
                'OPTIONS',
            ),
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
            'description' => 'some description, what is it doing',
        ),

        'api_id' => array (
            'title' => 'Api id',
            'type' => 'number',
        ),

        'element' => array (
            'title' => 'Element',
            'type' => 'bool',
            'description' => 'true for element, false for collection',
        ),

        'http_verb' => array (
            'title' => 'Http verb',
            'type' => 'enum',
            'options' => array (
                'GET',
                'POST',
                'PUT',
                'DELETE',
                'OPTIONS',
            ),
        ),
    ),
);
