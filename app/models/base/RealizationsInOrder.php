<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model RealizationsInOrder
 * Automatically generated abstract model class
 *
 * @method \Model\RealizationsInOrder[] get() public static function get(array $columns = array()) returns an array of models RealizationsInOrder
 * @method null|\Model\RealizationsInOrder first() public static function first(array $columns = array()) returns model RealizationsInOrder
 *
 * @property int $id Primary key
 * @property int $orderId
 * @property int $realizationId
 * @property int $amount
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Order|null realizationsInOrdersOrder Foreign model related via many belongs to one relation 
 * @property-read \Model\Realization|null realization Foreign model related via many belongs to one relation 
 */
abstract class RealizationsInOrder extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'realizations_in_orders';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'orderId' => array('db_type' => 'integer'),
                                'realizationId' => array('db_type' => 'integer'),
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
                                'orderId' => 'order_id',
                                'realizationId' => 'realization_id',
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
                                    'order_id',
                                    'realization_id',
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

                                    'order_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'orders',
                                            'id',
                                        ),
                                    ),

                                    'realization_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'realizations',
                                            'id',
                                        ),
                                    ),

                                    'amount' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
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

                                    'order_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'realization_id' => array (
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
                                        'realizationsInOrdersOrder' => '\\Model\\Order',
                                        'realization' => '\\Model\\Realization',
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
                    'order_id' => 'order_id',
                    'realization_id' => 'realization_id',
                    'amount' => 'amount',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function realizationsInOrdersOrder()
    {
        return $this->belongsTo('\\Model\\Order', 'order_id', null, 'order');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function realization()
    {
        return $this->belongsTo('\\Model\\Realization', 'realization_id', null, 'realization');
    }

}
