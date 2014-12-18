<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Language
 *
 */

return array(
    'title' => 'Languages',
    'single' => 'language',
    'model' => '\\Model\\Language',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'identifier' => array (
            'title' => 'Identifier',
        ),

        'english_name' => array (
            'title' => 'English name',
        ),

        'german_name' => array (
            'title' => 'German name',
        ),

        'native_name' => array (
            'title' => 'Native name',
        ),

        'fallback_language_id' => array (
            'title' => 'Fallback language id',
        ),

        'fallbackLanguage' => array (
            'title' => 'Fallback language',
            'relationship' => 'fallbackLanguage',
            'select' => '(:table).identifier',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'languagesServices' => array (
            'title' => 'Languages services',
            'relationship' => 'languagesServices',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'defaultLanguageServices' => array (
            'title' => 'Default language services',
            'relationship' => 'defaultLanguageServices',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'translations' => array (
            'title' => 'Translations',
            'relationship' => 'translations',
            'select' => 'GROUP_CONCAT((:table).text)',
        ),

        'languages' => array (
            'title' => 'Languages',
            'relationship' => 'languages',
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
            'description' => 'unique string used in API - language code with country, e.g. DE_AT',
        ),

        'english_name' => array (
            'title' => 'English name',
            'type' => 'text',
        ),

        'german_name' => array (
            'title' => 'German name',
            'type' => 'text',
        ),

        'native_name' => array (
            'title' => 'Native name',
            'type' => 'text',
        ),

        'fallbackLanguage' => array (
            'title' => 'Fallback language',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'languagesServices' => array (
            'title' => 'Languages services',
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
            'description' => 'unique string used in API - language code with country, e.g. DE_AT',
        ),

        'english_name' => array (
            'title' => 'English name',
            'type' => 'text',
        ),

        'german_name' => array (
            'title' => 'German name',
            'type' => 'text',
        ),

        'native_name' => array (
            'title' => 'Native name',
            'type' => 'text',
        ),

        'fallback_language_id' => array (
            'title' => 'Fallback language id',
            'type' => 'number',
        ),
    ),
);
