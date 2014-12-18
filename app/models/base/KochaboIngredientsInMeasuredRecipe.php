<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model KochaboIngredientsInMeasuredRecipe
 * Automatically generated abstract model class
 *
 * @method \Model\KochaboIngredientsInMeasuredRecipe[] get() public static function get(array $columns = array()) returns an array of models KochaboIngredientsInMeasuredRecipe
 * @method null|\Model\KochaboIngredientsInMeasuredRecipe first() public static function first(array $columns = array()) returns model KochaboIngredientsInMeasuredRecipe
 *
 * @property int $id Primary key
 * @property string $amount
 * @property int $measuredRecipeId
 * @property int $ingredientId
 * @property int $measureId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\KochaboMeasuredRecipe|null measuredRecipe Foreign model related via many belongs to one relation 
 * @property-read \Model\KochaboIngredient|null ingredient Foreign model related via many belongs to one relation 
 * @property-read \Model\KochaboMeasure|null measure Foreign model related via many belongs to one relation 
 */
abstract class KochaboIngredientsInMeasuredRecipe extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'kochabo_ingredients_in_measured_recipes';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'amount' => array('db_type' => 'varchar'),
                                'measuredRecipeId' => array('db_type' => 'integer'),
                                'ingredientId' => array('db_type' => 'integer'),
                                'measureId' => array('db_type' => 'integer'),
                                'createdAt' => array('db_type' => 'datetime'),
                                'updatedAt' => array('db_type' => 'datetime'),
                            );


    /**
     * Property name (usually camel cased) to column (attribute) name (usually snake cased) map
     *
     * @var array
     */
    protected $attributeMap = array(
                                'id' => 'id',
                                'amount' => 'amount',
                                'measuredRecipeId' => 'measured_recipe_id',
                                'ingredientId' => 'ingredient_id',
                                'measureId' => 'measure_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'amount',
                                    'measured_recipe_id',
                                    'ingredient_id',
                                    'measure_id',
                                );


    /**
     * Rules for validation
     *
     * @var array
     */
    public static $rules = array(
                                    'id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                    ),

                                    'amount' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'regex',
                                            '#^\\-? *[0-9]* *([0-9]+ */ *[1-9][0-9]*)?$#',
                                        ),
                                    ),

                                    'measured_recipe_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'kochabo_measured_recipes',
                                            'id',
                                        ),
                                    ),

                                    'ingredient_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'kochabo_ingredients',
                                            'id',
                                        ),
                                    ),

                                    'measure_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'kochabo_measures',
                                            'id',
                                        ),
                                    ),
                                );


    /**
     * Rules for modifications before validation
     *
     * @var array
     */
    public static $modificationRulesBeforeValidation = array();


    /**
     * Rules for modifications after validation
     *
     * @var array
     */
    public static $modificationRulesAfterValidation = array(
                                    'id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'measured_recipe_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'ingredient_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'measure_id' => array (
                                        'non_printable_to_null',
                                    ),
                                );

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array(
                                        'measuredRecipe' => '\\Model\\KochaboMeasuredRecipe',
                                        'ingredient' => '\\Model\\KochaboIngredient',
                                        'measure' => '\\Model\\KochaboMeasure',
                                    );

    /**
     * Properties, which could be used for filtering
     *
     * @return array
     */
    public function getFilterableByProperties()
    {
        return array(
                    'id' => 'id',
                    'amount' => 'amount',
                    'measured_recipe_id' => 'measured_recipe_id',
                    'ingredient_id' => 'ingredient_id',
                    'measure_id' => 'measure_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measuredRecipe()
    {
        return $this->belongsTo('\\Model\\KochaboMeasuredRecipe', 'measured_recipe_id', null, 'measuredRecipe');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ingredient()
    {
        return $this->belongsTo('\\Model\\KochaboIngredient', 'ingredient_id', null, 'ingredient');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measure()
    {
        return $this->belongsTo('\\Model\\KochaboMeasure', 'measure_id', null, 'measure');
    }

}
