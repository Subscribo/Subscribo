<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model KochaboRecipe
 * Automatically generated abstract model class
 *
 * @method \Model\KochaboRecipe[] get() public static function get(array $columns = array()) returns an array of models KochaboRecipe
 * @method null|\Model\KochaboRecipe first() public static function first(array $columns = array()) returns model KochaboRecipe
 *
 * @property int $id Primary key
 * @property int $countryId
 * @property string $source
 * @property string $identifier unique string used in API
 * @property string $comment
 * @property string $name
 * @property string $originalName
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Country|null country Foreign model related via many belongs to one relation 
 * @property-read \Model\Picture|null picture Foreign model related via polymorphic one has one relation 
 * @property-read \Model\KochaboRecipeStep[] kochaboRecipeSteps A collection of foreign models related via has many relation 
 * @property-read \Model\KochaboMeasuredRecipe[] kochaboMeasuredRecipes A collection of foreign models related via has many relation 
 */
abstract class KochaboRecipe extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'kochabo_recipes';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'countryId' => array('db_type' => 'integer'),
                                'source' => array('db_type' => 'varchar'),
                                'identifier' => array('db_type' => 'varchar'),
                                'comment' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
                                'originalName' => array('db_type' => 'varchar'),
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
                                'countryId' => 'country_id',
                                'source' => 'source',
                                'identifier' => 'identifier',
                                'comment' => 'comment',
                                'name' => 'name',
                                'originalName' => 'original_name',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'country_id',
                                    'source',
                                    'identifier',
                                    'comment',
                                    'name',
                                    'original_name',
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

                                    'country_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'countries',
                                            'id',
                                        ),
                                    ),

                                    'source' => array (
                                        'max:255',
                                    ),

                                    'identifier' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'unique',
                                            'kochabo_recipes',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'comment' => array (
                                        'max:255',
                                    ),

                                    'name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'original_name' => array (
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

                                    'country_id' => array (
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
                                        'country' => '\\Model\\Country',
                                        'picture' => '\\Model\\Picture',
                                        'kochaboRecipeSteps' => '\\Model\\KochaboRecipeStep',
                                        'kochaboMeasuredRecipes' => '\\Model\\KochaboMeasuredRecipe',
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
                    'country_id' => 'country_id',
                    'source' => 'source',
                    'identifier' => 'identifier',
                    'comment' => 'comment',
                    'name' => 'name',
                    'original_name' => 'original_name',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('\\Model\\Country', 'country_id', null, 'country');
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

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboRecipeSteps()
    {
        return $this->hasMany('\\Model\\KochaboRecipeStep', 'recipe_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboMeasuredRecipes()
    {
        return $this->hasMany('\\Model\\KochaboMeasuredRecipe', 'recipe_id');
    }

}
