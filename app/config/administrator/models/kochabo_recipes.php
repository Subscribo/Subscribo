<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model KochaboRecipe
 *
 */

return array(
    'title' => 'Kochabo recipes',
    'single' => 'kochabo recipe',
    'model' => '\\Model\\KochaboRecipe',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'country_id' => array (
            'title' => 'Country id',
        ),

        'country' => array (
            'title' => 'Country',
            'relationship' => 'country',
            'select' => '(:table).identifier',
        ),

        'source' => array (
            'title' => 'Source',
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

        'original_name' => array (
            'title' => 'Original name',
        ),

        'created_at' => array (
            'title' => 'Created at',
        ),

        'updated_at' => array (
            'title' => 'Updated at',
        ),

        'kochaboRecipeSteps' => array (
            'title' => 'Kochabo recipe steps',
            'relationship' => 'kochaboRecipeSteps',
            'select' => 'GROUP_CONCAT((:table).identifier)',
        ),

        'kochaboMeasuredRecipes' => array (
            'title' => 'Kochabo measured recipes',
            'relationship' => 'kochaboMeasuredRecipes',
            'select' => 'GROUP_CONCAT((:table).recipe_id)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'country' => array (
            'title' => 'Country',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'source' => array (
            'title' => 'Source',
            'type' => 'text',
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

        'original_name' => array (
            'title' => 'Original name',
            'type' => 'text',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'country_id' => array (
            'title' => 'Country id',
            'type' => 'number',
        ),

        'source' => array (
            'title' => 'Source',
            'type' => 'text',
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

        'original_name' => array (
            'title' => 'Original name',
            'type' => 'text',
        ),
    ),
);
