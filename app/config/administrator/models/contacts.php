<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Contact
 *
 */

return array(
    'title' => 'Contacts',
    'single' => 'contact',
    'model' => '\\Model\\Contact',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'mobile_phone_number' => array (
            'title' => 'Mobile phone number',
        ),

        'landline_phone_number' => array (
            'title' => 'Landline phone number',
        ),

        'home_address_id' => array (
            'title' => 'Home address id',
        ),

        'homeAddress' => array (
            'title' => 'Home address',
            'relationship' => 'homeAddress',
            'select' => '(:table).city',
        ),

        'work_address_id' => array (
            'title' => 'Work address id',
        ),

        'workAddress' => array (
            'title' => 'Work address',
            'relationship' => 'workAddress',
            'select' => '(:table).city',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'persons' => array (
            'title' => 'Persons',
            'relationship' => 'persons',
            'select' => 'GROUP_CONCAT((:table).last_name)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'mobile_phone_number' => array (
            'title' => 'Mobile phone number',
            'type' => 'number',
            'description' => 'Phone number in international format without leading + or 00 (another possibility would be to save it as string',
        ),

        'landline_phone_number' => array (
            'title' => 'Landline phone number',
            'type' => 'number',
        ),

        'homeAddress' => array (
            'title' => 'Home address',
            'type' => 'relationship',
            'name_field' => 'city',
        ),

        'workAddress' => array (
            'title' => 'Work address',
            'type' => 'relationship',
            'name_field' => 'city',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'mobile_phone_number' => array (
            'title' => 'Mobile phone number',
            'type' => 'number',
            'description' => 'Phone number in international format without leading + or 00 (another possibility would be to save it as string',
        ),

        'landline_phone_number' => array (
            'title' => 'Landline phone number',
            'type' => 'number',
        ),

        'home_address_id' => array (
            'title' => 'Home address id',
            'type' => 'number',
        ),

        'work_address_id' => array (
            'title' => 'Work address id',
            'type' => 'number',
        ),
    ),
);
