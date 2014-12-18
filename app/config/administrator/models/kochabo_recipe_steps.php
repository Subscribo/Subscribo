<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model KochaboRecipeStep
 *
 */

return array(
    'title' => 'Kochabo recipe steps',
    'single' => 'kochabo recipe step',
    'model' => '\\Model\\KochaboRecipeStep',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'sequence' => array (
            'title' => 'Sequence',
        ),

        'recipe_id' => array (
            'title' => 'Recipe id',
        ),

        'recipe' => array (
            'title' => 'Recipe',
            'relationship' => 'recipe',
            'select' => '(:table).identifier',
        ),

        'identifier' => array (
            'title' => 'Identifier',
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

        'sequence' => array (
            'title' => 'Sequence',
            'type' => 'number',
        ),

        'recipe' => array (
            'title' => 'Recipe',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'identifier' => array (
            'title' => 'Identifier',
            'type' => 'text',
            'description' => 'unique string used in API',
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

        'sequence' => array (
            'title' => 'Sequence',
            'type' => 'number',
        ),

        'recipe_id' => array (
            'title' => 'Recipe id',
            'type' => 'number',
        ),

        'identifier' => array (
            'title' => 'Identifier',
            'type' => 'text',
            'description' => 'unique string used in API',
        ),

        'text' => array (
            'title' => 'Text',
            'type' => 'text',
        ),
    ),
);
