<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model ProductsInSubscription
 * Automatically generated abstract model class
 *
 * @method \Model\ProductsInSubscription[] get() public static function get(array $columns = array()) returns an array of models ProductsInSubscription
 * @method null|\Model\ProductsInSubscription first() public static function first(array $columns = array()) returns model ProductsInSubscription
 *
 * @property int $id Primary key
 * @property int $subscriptionId
 * @property int $productId
 * @property int $amount
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Subscription|null subscription Foreign model related via many belongs to one relation 
 * @property-read \Model\Product|null product Foreign model related via many belongs to one relation 
 */
abstract class ProductsInSubscription extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'products_in_subscriptions';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'subscriptionId' => array('db_type' => 'integer'),
                                'productId' => array('db_type' => 'integer'),
                                'amount' => array('db_type' => 'integer'),
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
                                'subscriptionId' => 'subscription_id',
                                'productId' => 'product_id',
                                'amount' => 'amount',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'subscription_id',
                                    'product_id',
                                    'amount',
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

                                    'subscription_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'subscriptions',
                                            'id',
                                        ),
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

                                    'amount' => array (
                                        'required',
                                        'integer',
                                        'between:-2147483648,2147483647',
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

                                    'subscription_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'product_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'amount' => array (
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
                                        'subscription' => '\\Model\\Subscription',
                                        'product' => '\\Model\\Product',
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
                    'subscription_id' => 'subscription_id',
                    'product_id' => 'product_id',
                    'amount' => 'amount',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo('\\Model\\Subscription', 'subscription_id', null, 'subscription');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\\Model\\Product', 'product_id', null, 'product');
    }

}
