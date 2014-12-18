<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Country
 *
 */

return array(
    'title' => 'Countries',
    'single' => 'country',
    'model' => '\\Model\\Country',
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

        'english_name' => array (
            'title' => 'English name',
        ),

        'german_name' => array (
            'title' => 'German name',
        ),

        'country_union' => array (
            'title' => 'Country union',
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

        'banks' => array (
            'title' => 'Banks',
            'relationship' => 'banks',
            'select' => 'GROUP_CONCAT((:table).name)',
        ),

        'states' => array (
            'title' => 'States',
            'relationship' => 'states',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'kochaboRecipes' => array (
            'title' => 'Kochabo recipes',
            'relationship' => 'kochaboRecipes',
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
            'description' => 'unique string used in API (e.g. AT, DE ...)',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
            'description' => 'Name of country in the official language of the country',
        ),

        'english_name' => array (
            'title' => 'English name',
            'type' => 'text',
        ),

        'german_name' => array (
            'title' => 'German name',
            'type' => 'text',
        ),

        'country_union' => array (
            'title' => 'Country union',
            'type' => 'enum',
            'options' => array (
                'EU',
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
            'description' => 'unique string used in API (e.g. AT, DE ...)',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
            'description' => 'Name of country in the official language of the country',
        ),

        'english_name' => array (
            'title' => 'English name',
            'type' => 'text',
        ),

        'german_name' => array (
            'title' => 'German name',
            'type' => 'text',
        ),

        'country_union' => array (
            'title' => 'Country union',
            'type' => 'enum',
            'options' => array (
                'EU',
            ),
        ),
    ),
);
