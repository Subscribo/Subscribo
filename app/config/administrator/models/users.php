<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model User
 *
 */

return array(
    'title' => 'Users',
    'single' => 'user',
    'model' => '\\Model\\User',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'username' => array (
            'title' => 'Username',
        ),

        'email' => array (
            'title' => 'Email',
        ),

        'type' => array (
            'title' => 'Type',
        ),

        'email_confirmed' => array (
            'title' => 'Email confirmed',
        ),

        'oauth' => array (
            'title' => 'Oauth',
        ),

        'fb_account' => array (
            'title' => 'Fb account',
        ),

        'person_id' => array (
            'title' => 'Person id',
        ),

        'person' => array (
            'title' => 'Person',
            'relationship' => 'person',
            'select' => '(:table).last_name',
        ),

        'default_delivery_address_id' => array (
            'title' => 'Default delivery address id',
        ),

        'defaultDeliveryAddress' => array (
            'title' => 'Default delivery address',
            'relationship' => 'defaultDeliveryAddress',
            'select' => '(:table).city',
        ),

        'default_billing_details_id' => array (
            'title' => 'Default billing details id',
        ),

        'defaultBillingDetail' => array (
            'title' => 'Default billing detail',
            'relationship' => 'defaultBillingDetail',
            'select' => '(:table).type',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'aclGroups' => array (
            'title' => 'Acl groups',
            'relationship' => 'aclGroups',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'accounts' => array (
            'title' => 'Accounts',
            'relationship' => 'accounts',
            'select' => 'GROUP_CONCAT((:table).user_id)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'username' => array (
            'title' => 'Username',
            'type' => 'text',
        ),

        'email' => array (
            'title' => 'Email',
            'type' => 'text',
        ),

        'password' => array (
            'title' => 'Password',
            'type' => 'password',
        ),

        'type' => array (
            'title' => 'Type',
            'type' => 'enum',
            'options' => array (
                'guest',
                'customer',
                'administrator',
                'superadmin',
            ),

            'description' => '(we should have here the basic separation between administrators and customers)',
        ),

        'remember_token' => array (
            'title' => 'Remember token',
            'type' => 'text',
            'description' => '(and other possible technical fields allowing logging in)',
        ),

        'email_confirmed' => array (
            'title' => 'Email confirmed',
            'type' => 'bool',
        ),

        'oauth' => array (
            'title' => 'Oauth',
            'type' => 'text',
        ),

        'fb_account' => array (
            'title' => 'Fb account',
            'type' => 'text',
        ),

        'person' => array (
            'title' => 'Person',
            'type' => 'relationship',
            'name_field' => 'last_name',
        ),

        'defaultDeliveryAddress' => array (
            'title' => 'Default delivery address',
            'type' => 'relationship',
            'name_field' => 'city',
        ),

        'defaultBillingDetail' => array (
            'title' => 'Default billing detail',
            'type' => 'relationship',
            'name_field' => 'type',
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

        'username' => array (
            'title' => 'Username',
            'type' => 'text',
        ),

        'email' => array (
            'title' => 'Email',
            'type' => 'text',
        ),

        'type' => array (
            'title' => 'Type',
            'type' => 'enum',
            'options' => array (
                'guest',
                'customer',
                'administrator',
                'superadmin',
            ),

            'description' => '(we should have here the basic separation between administrators and customers)',
        ),

        'email_confirmed' => array (
            'title' => 'Email confirmed',
            'type' => 'bool',
        ),

        'oauth' => array (
            'title' => 'Oauth',
            'type' => 'text',
        ),

        'fb_account' => array (
            'title' => 'Fb account',
            'type' => 'text',
        ),

        'person_id' => array (
            'title' => 'Person id',
            'type' => 'number',
        ),

        'default_delivery_address_id' => array (
            'title' => 'Default delivery address id',
            'type' => 'number',
        ),

        'default_billing_details_id' => array (
            'title' => 'Default billing details id',
            'type' => 'number',
        ),
    ),
);
