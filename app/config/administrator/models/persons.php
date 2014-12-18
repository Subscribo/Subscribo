<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Person
 *
 */

return array(
    'title' => 'Persons',
    'single' => 'person',
    'model' => '\\Model\\Person',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'salutation' => array (
            'title' => 'Salutation',
        ),

        'prefix' => array (
            'title' => 'Prefix',
        ),

        'first_name' => array (
            'title' => 'First name',
        ),

        'middle_names' => array (
            'title' => 'Middle names',
        ),

        'infix' => array (
            'title' => 'Infix',
        ),

        'last_name' => array (
            'title' => 'Last name',
        ),

        'suffix' => array (
            'title' => 'Suffix',
        ),

        'gender' => array (
            'title' => 'Gender',
        ),

        'date_of_birth' => array (
            'title' => 'Date of birth',
        ),

        'contact_id' => array (
            'title' => 'Contact id',
        ),

        'contact' => array (
            'title' => 'Contact',
            'relationship' => 'contact',
            'select' => '(:table).mobile_phone_number',
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
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'salutation' => array (
            'title' => 'Salutation',
            'type' => 'text',
        ),

        'prefix' => array (
            'title' => 'Prefix',
            'type' => 'text',
        ),

        'first_name' => array (
            'title' => 'First name',
            'type' => 'text',
        ),

        'middle_names' => array (
            'title' => 'Middle names',
            'type' => 'text',
        ),

        'infix' => array (
            'title' => 'Infix',
            'type' => 'text',
        ),

        'last_name' => array (
            'title' => 'Last name',
            'type' => 'text',
        ),

        'suffix' => array (
            'title' => 'Suffix',
            'type' => 'text',
        ),

        'gender' => array (
            'title' => 'Gender',
            'type' => 'enum',
            'options' => array (
                'man',
                'woman',
            ),
        ),

        'date_of_birth' => array (
            'title' => 'Date of birth',
            'type' => 'date',
        ),

        'contact' => array (
            'title' => 'Contact',
            'type' => 'relationship',
            'name_field' => 'mobile_phone_number',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'salutation' => array (
            'title' => 'Salutation',
            'type' => 'text',
        ),

        'prefix' => array (
            'title' => 'Prefix',
            'type' => 'text',
        ),

        'first_name' => array (
            'title' => 'First name',
            'type' => 'text',
        ),

        'middle_names' => array (
            'title' => 'Middle names',
            'type' => 'text',
        ),

        'infix' => array (
            'title' => 'Infix',
            'type' => 'text',
        ),

        'last_name' => array (
            'title' => 'Last name',
            'type' => 'text',
        ),

        'suffix' => array (
            'title' => 'Suffix',
            'type' => 'text',
        ),

        'gender' => array (
            'title' => 'Gender',
            'type' => 'enum',
            'options' => array (
                'man',
                'woman',
            ),
        ),

        'date_of_birth' => array (
            'title' => 'Date of birth',
            'type' => 'date',
        ),

        'contact_id' => array (
            'title' => 'Contact id',
            'type' => 'number',
        ),
    ),
);
