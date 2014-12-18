<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Subscription
 * Automatically generated abstract model class
 *
 * @method \Model\Subscription[] get() public static function get(array $columns = array()) returns an array of models Subscription
 * @method null|\Model\Subscription first() public static function first(array $columns = array()) returns model Subscription
 *
 * @property int $id Primary key
 * @property int $accountId
 * @property int $deliveryWindowTypeId
 * @property int $period 1 - weekly, 2 - every two weeks, 4 - monthly - other periods could be defined (possibility to use enum, but tiny int used for more flexibility)
 * @property int $status 1 - active, 2 - cancelled
 * @property string $start
 * @property string $end
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Account|null account Foreign model related via many belongs to one relation 
 * @property-read \Model\DeliveryWindowType|null deliveryWindowType Foreign model related via many belongs to one relation 
 * @property-read \Model\Veto[] vetos A collection of foreign models related via has many relation 
 * @property-read \Model\ProductsInSubscription[] productsInSubscriptions A collection of foreign models related via has many relation 
 * @property-read \Model\Order[] subscriptionOrders A collection of foreign models related via has many relation 
 */
abstract class Subscription extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'subscriptions';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'accountId' => array('db_type' => 'integer'),
                                'deliveryWindowTypeId' => array('db_type' => 'integer'),
                                'period' => array('db_type' => 'tinyinteger'),
                                'status' => array('db_type' => 'tinyinteger'),
                                'start' => array('db_type' => 'date'),
                                'end' => array('db_type' => 'date'),
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
                                'accountId' => 'account_id',
                                'deliveryWindowTypeId' => 'delivery_window_type_id',
                                'period' => 'period',
                                'status' => 'status',
                                'start' => 'start',
                                'end' => 'end',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'account_id',
                                    'delivery_window_type_id',
                                    'period',
                                    'status',
                                    'start',
                                    'end',
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

                                    'account_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'accounts',
                                            'id',
                                        ),
                                    ),

                                    'delivery_window_type_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'delivery_window_types',
                                            'id',
                                        ),
                                    ),

                                    'period' => array (
                                        'required',
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'status' => array (
                                        'required',
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'start' => array (
                                        'required',
                                        'date_format:Y-m-d',
                                    ),

                                    'end' => array (
                                        'required',
                                        'date_format:Y-m-d',
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

                                    'account_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'delivery_window_type_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'period' => array (
                                        'non_printable_to_null',
                                    ),

                                    'status' => array (
                                        'non_printable_to_null',
                                    ),

                                    'start' => array (
                                        'non_printable_to_null',
                                    ),

                                    'end' => array (
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
                                        'account' => '\\Model\\Account',
                                        'deliveryWindowType' => '\\Model\\DeliveryWindowType',
                                        'vetos' => '\\Model\\Veto',
                                        'productsInSubscriptions' => '\\Model\\ProductsInSubscription',
                                        'subscriptionOrders' => '\\Model\\Order',
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
                    'account_id' => 'account_id',
                    'delivery_window_type_id' => 'delivery_window_type_id',
                    'period' => 'period',
                    'status' => 'status',
                    'start' => 'start',
                    'end' => 'end',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('\\Model\\Account', 'account_id', null, 'account');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryWindowType()
    {
        return $this->belongsTo('\\Model\\DeliveryWindowType', 'delivery_window_type_id', null, 'deliveryWindowType');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vetos()
    {
        return $this->hasMany('\\Model\\Veto', 'subscription_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productsInSubscriptions()
    {
        return $this->hasMany('\\Model\\ProductsInSubscription', 'subscription_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptionOrders()
    {
        return $this->hasMany('\\Model\\Order', 'subscription_id');
    }

}
