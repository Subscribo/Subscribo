<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model KochaboMeasuredRecipesInRealization
 * Automatically generated abstract model class
 *
 * @method \Model\KochaboMeasuredRecipesInRealization[] get() public static function get(array $columns = array()) returns an array of models KochaboMeasuredRecipesInRealization
 * @method null|\Model\KochaboMeasuredRecipesInRealization first() public static function first(array $columns = array()) returns model KochaboMeasuredRecipesInRealization
 *
 * @property int $id Primary key
 * @property int $realizationId
 * @property int $measuredRecipeId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Realization|null realization Foreign model related via many belongs to one relation 
 * @property-read \Model\KochaboMeasuredRecipe|null measuredRecipe Foreign model related via many belongs to one relation 
 */
abstract class KochaboMeasuredRecipesInRealization extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'kochabo_measured_recipes_in_realizations';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'realizationId' => array('db_type' => 'integer'),
                                'measuredRecipeId' => array('db_type' => 'integer'),
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
                                'realizationId' => 'realization_id',
                                'measuredRecipeId' => 'measured_recipe_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'realization_id',
                                    'measured_recipe_id',
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

                                    'realization_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'realizations',
                                            'id',
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

                                    'realization_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'measured_recipe_id' => array (
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
                                        'realization' => '\\Model\\Realization',
                                        'measuredRecipe' => '\\Model\\KochaboMeasuredRecipe',
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
                    'realization_id' => 'realization_id',
                    'measured_recipe_id' => 'measured_recipe_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function realization()
    {
        return $this->belongsTo('\\Model\\Realization', 'realization_id', null, 'realization');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measuredRecipe()
    {
        return $this->belongsTo('\\Model\\KochaboMeasuredRecipe', 'measured_recipe_id', null, 'measuredRecipe');
    }

}
