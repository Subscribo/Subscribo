<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Order
 * Automatically generated abstract model class
 *
 * @method \Model\Order[] get() public static function get(array $columns = array()) returns an array of models Order
 * @method null|\Model\Order first() public static function first(array $columns = array()) returns model Order
 *
 * @property int $id Primary key
 * @property string $type
 * @property int $status 1 - ordering / 2 - ordered / 3 - prepared / 4 - send / 5 - delivered / 6 - returned, 7 -  cancelled...)
 * @property int $paymentId
 * @property int $deliveryId
 * @property int $deliveryWindowId
 * @property string $anticipatedDeliveryStart
 * @property string $anticipatedDeliveryEnd
 * @property int $subscriptionId
 * @property int $accountId
 * @property int $shippingAddressId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Payment|null payment Foreign model related via many belongs to one relation 
 * @property-read \Model\Delivery|null delivery Foreign model related via many belongs to one relation 
 * @property-read \Model\DeliveryWindow|null deliveryWindow Foreign model related via many belongs to one relation 
 * @property-read \Model\Subscription|null subscription Foreign model related via many belongs to one relation 
 * @property-read \Model\Account|null account Foreign model related via many belongs to one relation 
 * @property-read \Model\Address|null shippingAddress Foreign model related via many belongs to one relation 
 * @property-read \Model\RealizationsInOrder[] realizationsInOrders A collection of foreign models related via has many relation 
 */
abstract class Order extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'orders';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'type' => array('db_type' => 'enum'),
                                'status' => array('db_type' => 'tinyinteger'),
                                'paymentId' => array('db_type' => 'integer'),
                                'deliveryId' => array('db_type' => 'integer'),
                                'deliveryWindowId' => array('db_type' => 'integer'),
                                'anticipatedDeliveryStart' => array('db_type' => 'datetime'),
                                'anticipatedDeliveryEnd' => array('db_type' => 'datetime'),
                                'subscriptionId' => array('db_type' => 'integer'),
                                'accountId' => array('db_type' => 'integer'),
                                'shippingAddressId' => array('db_type' => 'integer'),
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
                                'type' => 'type',
                                'status' => 'status',
                                'paymentId' => 'payment_id',
                                'deliveryId' => 'delivery_id',
                                'deliveryWindowId' => 'delivery_window_id',
                                'anticipatedDeliveryStart' => 'anticipated_delivery_start',
                                'anticipatedDeliveryEnd' => 'anticipated_delivery_end',
                                'subscriptionId' => 'subscription_id',
                                'accountId' => 'account_id',
                                'shippingAddressId' => 'shipping_address_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'type',
                                    'status',
                                    'payment_id',
                                    'delivery_id',
                                    'delivery_window_id',
                                    'anticipated_delivery_start',
                                    'anticipated_delivery_end',
                                    'subscription_id',
                                    'account_id',
                                    'shipping_address_id',
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

                                    'type' => array (
                                        'required',
                                        array (
                                            'in',
                                            'automatic',
                                            'manual',
                                        ),
                                    ),

                                    'status' => array (
                                        'required',
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'payment_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'payments',
                                            'id',
                                        ),
                                    ),

                                    'delivery_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'deliveries',
                                            'id',
                                        ),
                                    ),

                                    'delivery_window_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'delivery_windows',
                                            'id',
                                        ),
                                    ),

                                    'anticipated_delivery_start' => array (
                                        'date',
                                    ),

                                    'anticipated_delivery_end' => array (
                                        'date',
                                    ),

                                    'subscription_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'subscriptions',
                                            'id',
                                        ),
                                    ),

                                    'account_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'accounts',
                                            'id',
                                        ),
                                    ),

                                    'shipping_address_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'addresses',
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

                                    'type' => array (
                                        'non_printable_to_null',
                                    ),

                                    'status' => array (
                                        'non_printable_to_null',
                                    ),

                                    'payment_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'delivery_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'delivery_window_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'anticipated_delivery_start' => array (
                                        'non_printable_to_null',
                                    ),

                                    'anticipated_delivery_end' => array (
                                        'non_printable_to_null',
                                    ),

                                    'subscription_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'account_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'shipping_address_id' => array (
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
                                        'payment' => '\\Model\\Payment',
                                        'delivery' => '\\Model\\Delivery',
                                        'deliveryWindow' => '\\Model\\DeliveryWindow',
                                        'subscription' => '\\Model\\Subscription',
                                        'account' => '\\Model\\Account',
                                        'shippingAddress' => '\\Model\\Address',
                                        'realizationsInOrders' => '\\Model\\RealizationsInOrder',
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
                    'type' => 'type',
                    'status' => 'status',
                    'payment_id' => 'payment_id',
                    'delivery_id' => 'delivery_id',
                    'delivery_window_id' => 'delivery_window_id',
                    'anticipated_delivery_start' => 'anticipated_delivery_start',
                    'anticipated_delivery_end' => 'anticipated_delivery_end',
                    'subscription_id' => 'subscription_id',
                    'account_id' => 'account_id',
                    'shipping_address_id' => 'shipping_address_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo('\\Model\\Payment', 'payment_id', null, 'payment');
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
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryWindow()
    {
        return $this->belongsTo('\\Model\\DeliveryWindow', 'delivery_window_id', null, 'deliveryWindow');
    }

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
    public function account()
    {
        return $this->belongsTo('\\Model\\Account', 'account_id', null, 'account');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingAddress()
    {
        return $this->belongsTo('\\Model\\Address', 'shipping_address_id', null, 'shippingAddress');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function realizationsInOrders()
    {
        return $this->hasMany('\\Model\\RealizationsInOrder', 'order_id');
    }

}
