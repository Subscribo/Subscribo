<?php

/**
 * Automatically generated configuration file for Frozennode Administrator - Model KochaboMeasuredRecipe
 *
 */

return array(
    'title' => 'Kochabo measured recipes',
    'single' => 'kochabo measured recipe',
    'model' => '\\Model\\KochaboMeasuredRecipe',
    'columns' => array (
        'id' => array (
            'title' => 'Id',
        ),

        'product_id' => array (
            'title' => 'Product id',
        ),

        'product' => array (
            'title' => 'Product',
            'relationship' => 'product',
            'select' => '(:table).identifier',
        ),

        'recipe_id' => array (
            'title' => 'Recipe id',
        ),

        'recipe' => array (
            'title' => 'Recipe',
            'relationship' => 'recipe',
            'select' => '(:table).identifier',
        ),

        'persons_count' => array (
            'title' => 'Persons count',
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

        'kochaboMeasuredRecipesInRealizations' => array (
            'title' => 'Kochabo measured recipes in realizations',
            'relationship' => 'kochaboMeasuredRecipesInRealizations',
            'select' => 'GROUP_CONCAT((:table).realization_id)',
        ),
    ),

    'edit_fields' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'product' => array (
            'title' => 'Product',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'recipe' => array (
            'title' => 'Recipe',
            'type' => 'relationship',
            'name_field' => 'identifier',
        ),

        'persons_count' => array (
            'title' => 'Persons count',
            'type' => 'number',
        ),
    ),

    'filters' => array (
        'id' => array (
            'title' => 'Id',
            'type' => 'key',
            'description' => 'Primary key',
        ),

        'product_id' => array (
            'title' => 'Product id',
            'type' => 'number',
        ),

        'recipe_id' => array (
            'title' => 'Recipe id',
            'type' => 'number',
        ),

        'persons_count' => array (
            'title' => 'Persons count',
            'type' => 'number',
        ),
    ),
);
