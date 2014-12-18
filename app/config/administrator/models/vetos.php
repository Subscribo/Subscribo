<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Veto
 *
 */

return array(
    'title' => 'Vetos',
    'single' => 'veto',
    'model' => '\\Model\\Veto',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'start' => array (
            'title' => 'Start',
        ),

        'end' => array (
            'title' => 'End',
        ),

        'subscription_id' => array (
            'title' => 'Subscription id',
        ),

        'subscription' => array (
            'title' => 'Subscription',
            'relationship' => 'subscription',
            'select' => '(:table).account_id',
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

        'start' => array (
            'title' => 'Start',
            'type' => 'date',
        ),

        'end' => array (
            'title' => 'End',
            'type' => 'date',
        ),

        'subscription' => array (
            'title' => 'Subscription',
            'type' => 'relationship',
            'name_field' => 'account_id',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'date',
        ),

        'end' => array (
            'title' => 'End',
            'type' => 'date',
        ),

        'subscription_id' => array (
            'title' => 'Subscription id',
            'type' => 'number',
        ),
    ),
);
