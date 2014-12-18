<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Service
 *
 */

return array(
    'title' => 'Services',
    'single' => 'service',
    'model' => '\\Model\\Service',
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

        'url' => array (
            'title' => 'Url',
        ),

        'default_language_id' => array (
            'title' => 'Default language id',
        ),

        'defaultLanguage' => array (
            'title' => 'Default language',
            'relationship' => 'defaultLanguage',
            'select' => '(:table).identifier',
        ),

        'operator_id' => array (
            'title' => 'Operator id',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'servicePools' => array (
            'title' => 'Service pools',
            'relationship' => 'servicePools',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'apis' => array (
            'title' => 'Apis',
            'relationship' => 'apis',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'availableLanguages' => array (
            'title' => 'Available languages',
            'relationship' => 'availableLanguages',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'accounts' => array (
            'title' => 'Accounts',
            'relationship' => 'accounts',
            'select' => 'GROUP_CONCAT((:table).user_id)',
        ),

        'products' => array (
            'title' => 'Products',
            'relationship' => 'products',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'deliveries' => array (
            'title' => 'Deliveries',
            'relationship' => 'deliveries',
            'select' => 'GROUP_CONCAT((:table).start)',
        ),

        'deliveryWindowTypes' => array (
            'title' => 'Delivery window types',
            'relationship' => 'deliveryWindowTypes',
            'select' => 'GROUP_CONCAT((:table).day_of_week)',
        ),

        'tagGroups' => array (
            'title' => 'Tag groups',
            'relationship' => 'tagGroups',
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
            'description' => 'some technical comment to particular row, such as “do not use now, just for testing”',
        ),

        'url' => array (
            'title' => 'Url',
            'type' => 'text',
        ),

        'defaultLanguage' => array (
            'title' => 'Default language',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'operator_id' => array (
            'title' => 'Operator id',
            'type' => 'number',
            'description' => 'operators table does not exist in current version of the schema, so the relation type is no_relation',
            'value' => 1,
        ),

        'servicePools' => array (
            'title' => 'Service pools',
            'type' => 'relationship',
            'name_field' => 'identifier',
            'ordering' => 'ordering',
        ),

        'apis' => array (
            'title' => 'Apis',
            'type' => 'relationship',
            'name_field' => 'identifier',
            'ordering' => 'ordering',
        ),

        'availableLanguages' => array (
            'title' => 'Available languages',
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
            'description' => 'some technical comment to particular row, such as “do not use now, just for testing”',
        ),

        'url' => array (
            'title' => 'Url',
            'type' => 'text',
        ),

        'default_language_id' => array (
            'title' => 'Default language id',
            'type' => 'number',
        ),

        'operator_id' => array (
            'title' => 'Operator id',
            'type' => 'number',
            'description' => 'operators table does not exist in current version of the schema, so the relation type is no_relation',
        ),
    ),
);
