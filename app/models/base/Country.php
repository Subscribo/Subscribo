<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Country
 * Automatically generated abstract model class
 *
 * @method \Model\Country[] get() public static function get(array $columns = array()) returns an array of models Country
 * @method null|\Model\Country first() public static function first(array $columns = array()) returns model Country
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API (e.g. AT, DE ...)
 * @property string $name Name of country in the official language of the country
 * @property string $englishName
 * @property string $germanName
 * @property string $countryUnion
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Address[] addresses A collection of foreign models related via has many relation 
 * @property-read \Model\Bank[] banks A collection of foreign models related via has many relation 
 * @property-read \Model\State[] states A collection of foreign models related via has many relation 
 * @property-read \Model\KochaboRecipe[] kochaboRecipes A collection of foreign models related via has many relation 
 */
abstract class Country extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'countries';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
                                'englishName' => array('db_type' => 'varchar'),
                                'germanName' => array('db_type' => 'varchar'),
                                'countryUnion' => array('db_type' => 'enum'),
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
                                'name' => 'name',
                                'englishName' => 'english_name',
                                'germanName' => 'german_name',
                                'countryUnion' => 'country_union',
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
                                    'name',
                                    'english_name',
                                    'german_name',
                                    'country_union',
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
                                            'countries',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'english_name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'german_name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'country_union' => array (
                                        array (
                                            'in',
                                            'EU',
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

                                    'country_union' => array (
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
                                        'addresses' => '\\Model\\Address',
                                        'banks' => '\\Model\\Bank',
                                        'states' => '\\Model\\State',
                                        'kochaboRecipes' => '\\Model\\KochaboRecipe',
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
                    'name' => 'name',
                    'english_name' => 'english_name',
                    'german_name' => 'german_name',
                    'country_union' => 'country_union',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany('\\Model\\Address', 'country_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany('\\Model\\Bank', 'country_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->hasMany('\\Model\\State', 'country_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboRecipes()
    {
        return $this->hasMany('\\Model\\KochaboRecipe', 'country_id');
    }

}
