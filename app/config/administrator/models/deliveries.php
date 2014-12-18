<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model Delivery
 *
 */

return array(
    'title' => 'Deliveries',
    'single' => 'delivery',
    'model' => '\\Model\\Delivery',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'start' => array (
            'title' => 'Start',
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

        'realizations' => array (
            'title' => 'Realizations',
            'relationship' => 'realizations',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'deliveryOrders' => array (
            'title' => 'Delivery orders',
            'relationship' => 'deliveryOrders',
            'select' => 'GROUP_CONCAT((:table).type)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'date',
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
        ),

        'start' => array (
            'title' => 'Start',
            'type' => 'date',
        ),

        'service_id' => array (
            'title' => 'Service id',
            'type' => 'number',
        ),
    ),
);
