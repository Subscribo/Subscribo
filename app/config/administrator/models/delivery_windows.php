<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model DeliveryWindow
 *
 */

return array(
    'title' => 'Delivery windows',
    'single' => 'delivery window',
    'model' => '\\Model\\DeliveryWindow',
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

        'delivery_window_type_id' => array (
            'title' => 'Delivery window type id',
        ),

        'deliveryWindowType' => array (
            'title' => 'Delivery window type',
            'relationship' => 'deliveryWindowType',
            'select' => '(:table).day_of_week',
        ),

        'delivery_id' => array (
            'title' => 'Delivery id',
        ),

        'delivery' => array (
            'title' => 'Delivery',
            'relationship' => 'delivery',
            'select' => '(:table).start',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'deliveryWindowOrders' => array (
            'title' => 'Delivery window orders',
            'relationship' => 'deliveryWindowOrders',
            'select' => 'GROUP_CONCAT((:table).type)',
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
            'type' => 'datetime',
        ),

        'end' => array (
            'title' => 'End',
            'type' => 'datetime',
        ),

        'deliveryWindowType' => array (
            'title' => 'Delivery window type',
            'type' => 'relationship',
            'name_field' => 'day_of_week',
        ),

        'delivery' => array (
            'title' => 'Delivery',
            'type' => 'relationship',
            'name_field' => 'start',
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
            'type' => 'datetime',
        ),

        'end' => array (
            'title' => 'End',
            'type' => 'datetime',
        ),

        'delivery_window_type_id' => array (
            'title' => 'Delivery window type id',
            'type' => 'number',
        ),

        'delivery_id' => array (
            'title' => 'Delivery id',
            'type' => 'number',
        ),
    ),
);
