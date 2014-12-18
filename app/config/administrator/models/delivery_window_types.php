<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model DeliveryWindowType
 *
 */

return array(
    'title' => 'Delivery window types',
    'single' => 'delivery window type',
    'model' => '\\Model\\DeliveryWindowType',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'day_of_week' => array (
            'title' => 'Day of week',
        ),

        'start' => array (
            'title' => 'Start',
        ),

        'duration' => array (
            'title' => 'Duration',
        ),

        'service_id' => array (
            'title' => 'Service id',
        ),

        'service' => array (
            'title' => 'Service',
            'relationship' => 'service',
            'select' => '(:table).identifier',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'deliveryWindows' => array (
            'title' => 'Delivery windows',
            'relationship' => 'deliveryWindows',
            'select' => 'GROUP_CONCAT((:table).start)',
        ),

        'subscriptions' => array (
            'title' => 'Subscriptions',
            'relationship' => 'subscriptions',
            'select' => 'GROUP_CONCAT((:table).account_id)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'day_of_week' => array (
            'title' => 'Day of week',
            'type' => 'number',
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'time',
        ),

        'duration' => array (
            'title' => 'Duration',
            'type' => 'number',
        ),

        'service' => array (
            'title' => 'Service',
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

        'day_of_week' => array (
            'title' => 'Day of week',
            'type' => 'number',
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'time',
        ),

        'duration' => array (
            'title' => 'Duration',
            'type' => 'number',
        ),

        'service_id' => array (
            'title' => 'Service id',
            'type' => 'number',
        ),
    ),
);
