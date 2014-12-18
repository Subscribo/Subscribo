<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model KochaboMeasuredRecipesInRealization
 *
 */

return array(
    'title' => 'Kochabo measured recipes in realizations',
    'single' => 'kochabo measured recipes in realization',
    'model' => '\\Model\\KochaboMeasuredRecipesInRealization',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'realization_id' => array (
            'title' => 'Realization id',
        ),

        'realization' => array (
            'title' => 'Realization',
            'relationship' => 'realization',
            'select' => '(:table).identifier',
        ),

        'measured_recipe_id' => array (
            'title' => 'Measured recipe id',
        ),

        'measuredRecipe' => array (
            'title' => 'Measured recipe',
            'relationship' => 'measuredRecipe',
            'select' => '(:table).recipe_id',
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

        'realization' => array (
            'title' => 'Realization',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'measuredRecipe' => array (
            'title' => 'Measured recipe',
            'type' => 'relationship',
            'name_field' => 'recipe_id',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'realization_id' => array (
            'title' => 'Realization id',
            'type' => 'number',
        ),

        'measured_recipe_id' => array (
            'title' => 'Measured recipe id',
            'type' => 'number',
        ),
    ),
);
