<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Product
 * Automatically generated abstract model class
 *
 * @method \Model\Product[] get() public static function get(array $columns = array()) returns an array of models Product
 * @method null|\Model\Product first() public static function first(array $columns = array()) returns model Product
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property int $serviceId
 * @property bool $standalone
 * @property string $name
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Service|null service Foreign model related via many belongs to one relation 
 * @property-read \Model\Realization[] realizations A collection of foreign models related via has many relation 
 * @property-read \Model\ProductsInSubscription[] productsInSubscriptions A collection of foreign models related via has many relation 
 * @property-read \Model\KochaboMeasuredRecipe[] kochaboMeasuredRecipes A collection of foreign models related via has many relation 
 */
abstract class Product extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'products';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'serviceId' => array('db_type' => 'integer'),
                                'standalone' => array('db_type' => 'boolean'),
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
                                'serviceId' => 'service_id',
                                'standalone' => 'standalone',
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
                                    'service_id',
                                    'standalone',
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
                                            'products',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'service_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'services',
                                            'id',
                                        ),
                                    ),

                                    'standalone' => array (
                                        'required',
                                        'boolean',
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

                                    'service_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'standalone' => array (
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
                                        'service' => '\\Model\\Service',
                                        'realizations' => '\\Model\\Realization',
                                        'productsInSubscriptions' => '\\Model\\ProductsInSubscription',
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
                    'identifier' => 'identifier',
                    'service_id' => 'service_id',
                    'standalone' => 'standalone',
                    'name' => 'name',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo('\\Model\\Service', 'service_id', null, 'service');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function realizations()
    {
        return $this->hasMany('\\Model\\Realization', 'product_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productsInSubscriptions()
    {
        return $this->hasMany('\\Model\\ProductsInSubscription', 'product_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboMeasuredRecipes()
    {
        return $this->hasMany('\\Model\\KochaboMeasuredRecipe', 'product_id');
    }

}
