<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model KochaboRecipeStep
 * Automatically generated abstract model class
 *
 * @method \Model\KochaboRecipeStep[] get() public static function get(array $columns = array()) returns an array of models KochaboRecipeStep
 * @method null|\Model\KochaboRecipeStep first() public static function first(array $columns = array()) returns model KochaboRecipeStep
 *
 * @property int $id Primary key
 * @property int $sequence
 * @property int $recipeId
 * @property string $identifier unique string used in API
 * @property string $text
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\KochaboRecipe|null recipe Foreign model related via many belongs to one relation 
 * @property-read \Model\Picture|null picture Foreign model related via polymorphic one has one relation 
 */
abstract class KochaboRecipeStep extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'kochabo_recipe_steps';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'sequence' => array('db_type' => 'integer'),
                                'recipeId' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'text' => array('db_type' => 'varchar'),
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
                                'sequence' => 'sequence',
                                'recipeId' => 'recipe_id',
                                'identifier' => 'identifier',
                                'text' => 'text',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'sequence',
                                    'recipe_id',
                                    'identifier',
                                    'text',
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

                                    'sequence' => array (
                                        'required',
                                        'integer',
                                        'between:-2147483648,2147483647',
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

                                    'identifier' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'unique',
                                            'kochabo_recipe_steps',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'text' => array (
                                        'required',
                                        'max:255',
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

                                    'sequence' => array (
                                        'non_printable_to_null',
                                    ),

                                    'recipe_id' => array (
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
                                        'recipe' => '\\Model\\KochaboRecipe',
                                        'picture' => '\\Model\\Picture',
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
                    'sequence' => 'sequence',
                    'recipe_id' => 'recipe_id',
                    'identifier' => 'identifier',
                    'text' => 'text',
                );
    }



    /* Model specific methods follows */


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
     * Relation definition. Type: polymorphic one has one
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function picture()
    {
        return $this->morphOne('\\Model\\Picture', 'picturable', 'picturable_type', 'picturable_id');
    }

}
