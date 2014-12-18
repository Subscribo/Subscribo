<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model DeliveryWindow
 * Automatically generated abstract model class
 *
 * @method \Model\DeliveryWindow[] get() public static function get(array $columns = array()) returns an array of models DeliveryWindow
 * @method null|\Model\DeliveryWindow first() public static function first(array $columns = array()) returns model DeliveryWindow
 *
 * @property int $id Primary key
 * @property string $start
 * @property string $end
 * @property int $deliveryWindowTypeId
 * @property int $deliveryId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\DeliveryWindowType|null deliveryWindowType Foreign model related via many belongs to one relation 
 * @property-read \Model\Delivery|null delivery Foreign model related via many belongs to one relation 
 * @property-read \Model\Order[] deliveryWindowOrders A collection of foreign models related via has many relation 
 */
abstract class DeliveryWindow extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'delivery_windows';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'start' => array('db_type' => 'datetime'),
                                'end' => array('db_type' => 'datetime'),
                                'deliveryWindowTypeId' => array('db_type' => 'integer'),
                                'deliveryId' => array('db_type' => 'integer'),
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
                                'start' => 'start',
                                'end' => 'end',
                                'deliveryWindowTypeId' => 'delivery_window_type_id',
                                'deliveryId' => 'delivery_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'start',
                                    'end',
                                    'delivery_window_type_id',
                                    'delivery_id',
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

                                    'start' => array (
                                        'required',
                                        'date',
                                    ),

                                    'end' => array (
                                        'required',
                                        'date',
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

                                    'start' => array (
                                        'non_printable_to_null',
                                    ),

                                    'end' => array (
                                        'non_printable_to_null',
                                    ),

                                    'delivery_window_type_id' => array (
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
                                        'deliveryWindowType' => '\\Model\\DeliveryWindowType',
                                        'delivery' => '\\Model\\Delivery',
                                        'deliveryWindowOrders' => '\\Model\\Order',
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
                    'start' => 'start',
                    'end' => 'end',
                    'delivery_window_type_id' => 'delivery_window_type_id',
                    'delivery_id' => 'delivery_id',
                );
    }



    /* Model specific methods follows */


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
    public function deliveryWindowOrders()
    {
        return $this->hasMany('\\Model\\Order', 'delivery_window_id');
    }

}
