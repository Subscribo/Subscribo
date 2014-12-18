<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Realization
 * Automatically generated abstract model class
 *
 * @method \Model\Realization[] get() public static function get(array $columns = array()) returns an array of models Realization
 * @method null|\Model\Realization first() public static function first(array $columns = array()) returns model Realization
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $comment
 * @property int $productId
 * @property int $deliveryId
 * @property string $name
 * @property string $description
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Product|null product Foreign model related via many belongs to one relation 
 * @property-read \Model\Delivery|null delivery Foreign model related via many belongs to one relation 
 * @property-read \Model\RealizationsInOrder[] realizationsInOrders A collection of foreign models related via has many relation 
 * @property-read \Model\KochaboMeasuredRecipesInRealization[] kochaboMeasuredRecipesInRealizations A collection of foreign models related via has many relation 
 */
abstract class Realization extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'realizations';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'comment' => array('db_type' => 'varchar'),
                                'productId' => array('db_type' => 'integer'),
                                'deliveryId' => array('db_type' => 'integer'),
                                'name' => array('db_type' => 'varchar'),
                                'description' => array('db_type' => 'varchar'),
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
                                'productId' => 'product_id',
                                'deliveryId' => 'delivery_id',
                                'name' => 'name',
                                'description' => 'description',
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
                                    'product_id',
                                    'delivery_id',
                                    'name',
                                    'description',
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
                                            'realizations',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'comment' => array (
                                        'max:255',
                                    ),

                                    'product_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'products',
                                            'id',
                                        ),
                                    ),

                                    'delivery_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'deliveries',
                                            'id',
                                        ),
                                    ),

                                    'name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'description' => array (
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

                                    'product_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'delivery_id' => array (
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
                                        'delivery' => '\\Model\\Delivery',
                                        'realizationsInOrders' => '\\Model\\RealizationsInOrder',
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
                    'identifier' => 'identifier',
                    'comment' => 'comment',
                    'product_id' => 'product_id',
                    'delivery_id' => 'delivery_id',
                    'name' => 'name',
                    'description' => 'description',
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
    public function delivery()
    {
        return $this->belongsTo('\\Model\\Delivery', 'delivery_id', null, 'delivery');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function realizationsInOrders()
    {
        return $this->hasMany('\\Model\\RealizationsInOrder', 'realization_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kochaboMeasuredRecipesInRealizations()
    {
        return $this->hasMany('\\Model\\KochaboMeasuredRecipesInRealization', 'realization_id');
    }

}
