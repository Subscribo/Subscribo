<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model KochaboMeasuredRecipe
 * Automatically generated abstract model class
 *
 * @method \Model\KochaboMeasuredRecipe[] get() public static function get(array $columns = array()) returns an array of models KochaboMeasuredRecipe
 * @method null|\Model\KochaboMeasuredRecipe first() public static function first(array $columns = array()) returns model KochaboMeasuredRecipe
 *
 * @property int $id Primary key
 * @property int $productId
 * @property int $recipeId
 * @property int $personsCount
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Product|null product Foreign model related via many belongs to one relation 
 * @property-read \Model\KochaboRecipe|null recipe Foreign model related via many belongs to one relation 
 * @property-read \Model\KochaboIngredientsInMeasuredRecipe[] kochaboIngredientsInMeasuredRecipes A collection of foreign models related via has many relation 
 * @property-read \Model\KochaboMeasuredRecipesInRealization[] kochaboMeasuredRecipesInRealizations A collection of foreign models related via has many relation 
 */
abstract class KochaboMeasuredRecipe extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'kochabo_measured_recipes';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'productId' => array('db_type' => 'integer'),
                                'recipeId' => array('db_type' => 'integer'),
                                'personsCount' => array('db_type' => 'tinyinteger'),
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
                                'productId' => 'product_id',
                                'recipeId' => 'recipe_id',
                                'personsCount' => 'persons_count',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'product_id',
                                    'recipe_id',
                                    'persons_count',
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

                                    'product_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'products',
                                            'id',
                                        ),
                                    ),

                                    'recipe_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'kochabo_recipes',
                                            'id',
                                        ),
                                    ),

                                    'persons_count' => array (
                                        'required',
                                        'integer',
                                        'between:0,255',
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

                                    'product_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'recipe_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'persons_count' => array (
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
                                        'product' => '\\Model\\Product',
                                        'recipe' => '\\Model\\KochaboRecipe',
                                        'kochaboIngredientsInMeasuredRecipes' => '\\Model\\KochaboIngredientsInMeasuredRecipe',
                                        'kochaboMeasuredRecipesInRealizations' => '\\Model\\KochaboMeasuredRecipesInRealization',
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
                    'product_id' => 'product_id',
                    'recipe_id' => 'recipe_id',
                    'persons_count' => 'persons_count',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\\Model\\Product', 'product_id', null, 'product');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe()
    {
        return $this->belongsTo('\\Model\\KochaboRecipe', 'recipe_id', null, 'recipe');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboIngredientsInMeasuredRecipes()
    {
        return $this->hasMany('\\Model\\KochaboIngredientsInMeasuredRecipe', 'measured_recipe_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboMeasuredRecipesInRealizations()
    {
        return $this->hasMany('\\Model\\KochaboMeasuredRecipesInRealization', 'measured_recipe_id');
    }

}
