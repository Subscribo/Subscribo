<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Address
 *
 */

return array(
    'title' => 'Addresses',
    'single' => 'address',
    'model' => '\\Model\\Address',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'type' => array (
            'title' => 'Type',
        ),

        'first_line' => array (
            'title' => 'First line',
        ),

        'second_line' => array (
            'title' => 'Second line',
        ),

        'street' => array (
            'title' => 'Street',
        ),

        'house' => array (
            'title' => 'House',
        ),

        'stairway' => array (
            'title' => 'Stairway',
        ),

        'floor' => array (
            'title' => 'Floor',
        ),

        'apartment' => array (
            'title' => 'Apartment',
        ),

        'post_code' => array (
            'title' => 'Post code',
        ),

        'city' => array (
            'title' => 'City',
        ),

        'district' => array (
            'title' => 'District',
        ),

        'province' => array (
            'title' => 'Province',
        ),

        'state_id' => array (
            'title' => 'State id',
        ),

        'state' => array (
            'title' => 'State',
            'relationship' => 'state',
            'select' => '(:table).identifier',
        ),

        'country_id' => array (
            'title' => 'Country id',
        ),

        'country' => array (
            'title' => 'Country',
            'relationship' => 'country',
            'select' => '(:table).identifier',
        ),

        'country_union' => array (
            'title' => 'Country union',
        ),

        'gps_longitude' => array (
            'title' => 'Gps longitude',
        ),

        'gps_latitude' => array (
            'title' => 'Gps latitude',
        ),

        'contact_phone' => array (
            'title' => 'Contact phone',
        ),

        'delivery_information' => array (
            'title' => 'Delivery information',
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

        'homeAddressContacts' => array (
            'title' => 'Home address contacts',
            'relationship' => 'homeAddressContacts',
            'select' => 'GROUP_CONCAT((:table).mobile_phone_number)',
        ),

        'workAddressContacts' => array (
            'title' => 'Work address contacts',
            'relationship' => 'workAddressContacts',
            'select' => 'GROUP_CONCAT((:table).mobile_phone_number)',
        ),

        'billingDetails' => array (
            'title' => 'Billing details',
            'relationship' => 'billingDetails',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),

        'banks' => array (
            'title' => 'Banks',
            'relationship' => 'banks',
            'select' => 'GROUP_CONCAT((:table).name)',
        ),

        'shippingAddressOrders' => array (
            'title' => 'Shipping address orders',
            'relationship' => 'shippingAddressOrders',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'type' => array (
            'title' => 'Type',
            'type' => 'number',
            'value' => 1,
        ),

        'first_line' => array (
            'title' => 'First line',
            'type' => 'text',
        ),

        'second_line' => array (
            'title' => 'Second line',
            'type' => 'text',
        ),

        'street' => array (
            'title' => 'Street',
            'type' => 'text',
        ),

        'house' => array (
            'title' => 'House',
            'type' => 'text',
        ),

        'stairway' => array (
            'title' => 'Stairway',
            'type' => 'text',
        ),

        'floor' => array (
            'title' => 'Floor',
            'type' => 'text',
        ),

        'apartment' => array (
            'title' => 'Apartment',
            'type' => 'text',
        ),

        'post_code' => array (
            'title' => 'Post code',
            'type' => 'text',
        ),

        'city' => array (
            'title' => 'City',
            'type' => 'text',
            'description' => 'Settlement name',
        ),

        'district' => array (
            'title' => 'District',
            'type' => 'text',
            'description' => 'district of a city',
        ),

        'province' => array (
            'title' => 'Province',
            'type' => 'text',
            'description' => 'state/country subdivision',
        ),

        'state' => array (
            'title' => 'State',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'country' => array (
            'title' => 'Country',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'country_union' => array (
            'title' => 'Country union',
            'type' => 'enum',
            'options' => array (
                'EU',
            ),
        ),

        'gps_longitude' => array (
            'title' => 'Gps longitude',
            'type' => 'text',
        ),

        'gps_latitude' => array (
            'title' => 'Gps latitude',
            'type' => 'text',
        ),

        'contact_phone' => array (
            'title' => 'Contact phone',
            'type' => 'number',
            'description' => 'Phone number in international format without leading + or 00 (another possibility would be to save it as string',
        ),

        'delivery_information' => array (
            'title' => 'Delivery information',
            'type' => 'text',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'type' => array (
            'title' => 'Type',
            'type' => 'number',
        ),

        'first_line' => array (
            'title' => 'First line',
            'type' => 'text',
        ),

        'second_line' => array (
            'title' => 'Second line',
            'type' => 'text',
        ),

        'street' => array (
            'title' => 'Street',
            'type' => 'text',
        ),

        'house' => array (
            'title' => 'House',
            'type' => 'text',
        ),

        'stairway' => array (
            'title' => 'Stairway',
            'type' => 'text',
        ),

        'floor' => array (
            'title' => 'Floor',
            'type' => 'text',
        ),

        'apartment' => array (
            'title' => 'Apartment',
            'type' => 'text',
        ),

        'post_code' => array (
            'title' => 'Post code',
            'type' => 'text',
        ),

        'city' => array (
            'title' => 'City',
            'type' => 'text',
            'description' => 'Settlement name',
        ),

        'district' => array (
            'title' => 'District',
            'type' => 'text',
            'description' => 'district of a city',
        ),

        'province' => array (
            'title' => 'Province',
            'type' => 'text',
            'description' => 'state/country subdivision',
        ),

        'state_id' => array (
            'title' => 'State id',
            'type' => 'number',
            'description' => 'e.g. US state',
        ),

        'country_id' => array (
            'title' => 'Country id',
            'type' => 'number',
        ),

        'country_union' => array (
            'title' => 'Country union',
            'type' => 'enum',
            'options' => array (
                'EU',
            ),
        ),

        'gps_longitude' => array (
            'title' => 'Gps longitude',
            'type' => 'text',
        ),

        'gps_latitude' => array (
            'title' => 'Gps latitude',
            'type' => 'text',
        ),

        'contact_phone' => array (
            'title' => 'Contact phone',
            'type' => 'number',
            'description' => 'Phone number in international format without leading + or 00 (another possibility would be to save it as string',
        ),

        'delivery_information' => array (
            'title' => 'Delivery information',
            'type' => 'text',
        ),
    ),
);
