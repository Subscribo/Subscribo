<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model State
 *
 */

return array(
    'title' => 'States',
    'single' => 'state',
    'model' => '\\Model\\State',
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

        'country_id' => array (
            'title' => 'Country id',
        ),

        'country' => array (
            'title' => 'Country',
            'relationship' => 'country',
            'select' => '(:table).identifier',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'addresses' => array (
            'title' => 'Addresses',
            'relationship' => 'addresses',
            'select' => 'GROUP_CONCAT((:table).city)',
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
            'description' => 'Name of state in the official language ot the country',
        ),

        'country' => array (
            'title' => 'Country',
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
            'description' => 'Name of state in the official language ot the country',
        ),

        'country_id' => array (
            'title' => 'Country id',
            'type' => 'number',
        ),
    ),
);
