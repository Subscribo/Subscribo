<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Translation
 *
 */

return array(
    'title' => 'Translations',
    'single' => 'translation',
    'model' => '\\Model\\Translation',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'table_id' => array (
            'title' => 'Table id',
        ),

        'translationsTable' => array (
            'title' => 'Translations table',
            'relationship' => 'translationsTable',
            'select' => '(:table).identifier',
        ),

        'field_id' => array (
            'title' => 'Field id',
        ),

        'field' => array (
            'title' => 'Field',
            'relationship' => 'field',
            'select' => '(:table).identifier',
        ),

        'row_id' => array (
            'title' => 'Row id',
        ),

        'language_id' => array (
            'title' => 'Language id',
        ),

        'language' => array (
            'title' => 'Language',
            'relationship' => 'language',
            'select' => '(:table).identifier',
        ),

        'text' => array (
            'title' => 'Text',
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

        'translationsTable' => array (
            'title' => 'Translations table',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'field' => array (
            'title' => 'Field',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'row_id' => array (
            'title' => 'Row id',
            'type' => 'number',
        ),

        'language' => array (
            'title' => 'Language',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'text' => array (
            'title' => 'Text',
            'type' => 'text',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'table_id' => array (
            'title' => 'Table id',
            'type' => 'number',
        ),

        'field_id' => array (
            'title' => 'Field id',
            'type' => 'number',
        ),

        'row_id' => array (
            'title' => 'Row id',
            'type' => 'number',
        ),

        'language_id' => array (
            'title' => 'Language id',
            'type' => 'number',
        ),

        'text' => array (
            'title' => 'Text',
            'type' => 'text',
        ),
    ),
);
