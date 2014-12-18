<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model KochaboIngredientsInMeasuredRecipe
 *
 */

return array(
    'title' => 'Kochabo ingredients in measured recipes',
    'single' => 'kochabo ingredients in measured recipe',
    'model' => '\\Model\\KochaboIngredientsInMeasuredRecipe',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'amount' => array (
            'title' => 'Amount',
        ),

        'measured_recipe_id' => array (
            'title' => 'Measured recipe id',
        ),

        'measuredRecipe' => array (
            'title' => 'Measured recipe',
            'relationship' => 'measuredRecipe',
            'select' => '(:table).recipe_id',
        ),

        'ingredient_id' => array (
            'title' => 'Ingredient id',
        ),

        'ingredient' => array (
            'title' => 'Ingredient',
            'relationship' => 'ingredient',
            'select' => '(:table).identifier',
        ),

        'measure_id' => array (
            'title' => 'Measure id',
        ),

        'measure' => array (
            'title' => 'Measure',
            'relationship' => 'measure',
            'select' => '(:table).identifier',
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

        'amount' => array (
            'title' => 'Amount',
            'type' => 'text',
        ),

        'measuredRecipe' => array (
            'title' => 'Measured recipe',
            'type' => 'relationship',
            'name_field' => 'recipe_id',
        ),

        'ingredient' => array (
            'title' => 'Ingredient',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'measure' => array (
            'title' => 'Measure',
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

        'amount' => array (
            'title' => 'Amount',
            'type' => 'text',
        ),

        'measured_recipe_id' => array (
            'title' => 'Measured recipe id',
            'type' => 'number',
        ),

        'ingredient_id' => array (
            'title' => 'Ingredient id',
            'type' => 'number',
        ),

        'measure_id' => array (
            'title' => 'Measure id',
            'type' => 'number',
        ),
    ),
);
