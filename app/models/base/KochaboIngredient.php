<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model KochaboIngredient
 * Automatically generated abstract model class
 *
 * @method \Model\KochaboIngredient[] get() public static function get(array $columns = array()) returns an array of models KochaboIngredient
 * @method null|\Model\KochaboIngredient first() public static function first(array $columns = array()) returns model KochaboIngredient
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $comment
 * @property string $name
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Tag[] tags A collection of foreign models related via polymorphic many belongs to many relation 
 * @property-read \Model\KochaboIngredientsInMeasuredRecipe[] kochaboIngredientsInMeasuredRecipes A collection of foreign models related via has many relation 
 */
abstract class KochaboIngredient extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'kochabo_ingredients';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'comment' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
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
                                'identifier' => 'identifier',
                                'comment' => 'comment',
                                'name' => 'name',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'identifier',
                                    'comment',
                                    'name',
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

                                    'identifier' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'unique',
                                            'kochabo_ingredients',
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
                                );

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array(
                                        'tags' => '\\Model\\Tag',
                                        'kochaboIngredientsInMeasuredRecipes' => '\\Model\\KochaboIngredientsInMeasuredRecipe',
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
                    'identifier' => 'identifier',
                    'comment' => 'comment',
                    'name' => 'name',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: polymorphic many belongs to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany('\\Model\\Tag', 'tag_attachable', 'tag_attachables');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboIngredientsInMeasuredRecipes()
    {
        return $this->hasMany('\\Model\\KochaboIngredientsInMeasuredRecipe', 'ingredient_id');
    }

}
