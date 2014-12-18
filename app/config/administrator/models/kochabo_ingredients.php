<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model KochaboIngredient
 *
 */

return array(
    'title' => 'Kochabo ingredients',
    'single' => 'kochabo ingredient',
    'model' => '\\Model\\KochaboIngredient',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'identifier' => array (
            'title' => 'Identifier',
        ),

        'comment' => array (
            'title' => 'Comment',
        ),

        'name' => array (
            'title' => 'Name',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'kochaboIngredientsInMeasuredRecipes' => array (
            'title' => 'Kochabo ingredients in measured recipes',
            'relationship' => 'kochaboIngredientsInMeasuredRecipes',
            'select' => 'GROUP_CONCAT((:table).amount)',
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
            'description' => 'unique string used in API',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
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
            'description' => 'unique string used in API',
        ),

        'comment' => array (
            'title' => 'Comment',
            'type' => 'text',
        ),

        'name' => array (
            'title' => 'Name',
            'type' => 'text',
        ),
    ),
);
