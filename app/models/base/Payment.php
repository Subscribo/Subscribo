<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Payment
 * Automatically generated abstract model class
 *
 * @method \Model\Payment[] get() public static function get(array $columns = array()) returns an array of models Payment
 * @method null|\Model\Payment first() public static function first(array $columns = array()) returns model Payment
 *
 * @property int $id Primary key
 * @property int $type 1 - send_from_account / 2 - credit_card / 3 - direct_debit ...)
 * @property int $status 1 - planned / 2 - requested / 3 - paid / 4 - rejected / 5 - canceled / 6 - requested_back …)
 * @property string $amount
 * @property string $vat
 * @property int $currencyId
 * @property int $billingDetailId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Currency|null currency Foreign model related via many belongs to one relation 
 * @property-read \Model\BillingDetail|null billingDetail Foreign model related via many belongs to one relation 
 * @property-read \Model\Order[] paymentOrders A collection of foreign models related via has many relation 
 */
abstract class Payment extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'payments';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'type' => array('db_type' => 'tinyinteger'),
                                'status' => array('db_type' => 'tinyinteger'),
                                'amount' => array('db_type' => 'decimal'),
                                'vat' => array('db_type' => 'decimal'),
                                'currencyId' => array('db_type' => 'integer'),
                                'billingDetailId' => array('db_type' => 'integer'),
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
                                'amount' => 'amount',
                                'vat' => 'vat',
                                'currencyId' => 'currency_id',
                                'billingDetailId' => 'billing_detail_id',
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
                                    'amount',
                                    'vat',
                                    'currency_id',
                                    'billing_detail_id',
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
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'status' => array (
                                        'required',
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'amount' => array (
                                        'required',
                                        array (
                                            'regex',
                                            '#^\\-?0?[0-9]{0,8}(\\.[0-9]{0,2})?$#',
                                        ),
                                    ),

                                    'vat' => array (
                                        'required',
                                        array (
                                            'regex',
                                            '#^\\-?0?[0-9]{0,8}(\\.[0-9]{0,2})?$#',
                                        ),
                                    ),

                                    'currency_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'currencies',
                                            'id',
                                        ),
                                    ),

                                    'billing_detail_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'billing_details',
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

                                    'amount' => array (
                                        'non_printable_to_null',
                                    ),

                                    'vat' => array (
                                        'non_printable_to_null',
                                    ),

                                    'currency_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'billing_detail_id' => array (
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
                                        'currency' => '\\Model\\Currency',
                                        'billingDetail' => '\\Model\\BillingDetail',
                                        'paymentOrders' => '\\Model\\Order',
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
                    'amount' => 'amount',
                    'vat' => 'vat',
                    'currency_id' => 'currency_id',
                    'billing_detail_id' => 'billing_detail_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('\\Model\\Currency', 'currency_id', null, 'currency');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billingDetail()
    {
        return $this->belongsTo('\\Model\\BillingDetail', 'billing_detail_id', null, 'billingDetail');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentOrders()
    {
        return $this->hasMany('\\Model\\Order', 'payment_id');
    }

}
