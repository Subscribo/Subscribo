<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Delivery
 * Automatically generated abstract model class
 *
 * @method \Model\Delivery[] get() public static function get(array $columns = array()) returns an array of models Delivery
 * @method null|\Model\Delivery first() public static function first(array $columns = array()) returns model Delivery
 *
 * @property int $id
 * @property string $start
 * @property int $serviceId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Service|null service Foreign model related via many belongs to one relation 
 * @property-read \Model\DeliveryWindow[] deliveryWindows A collection of foreign models related via has many relation 
 * @property-read \Model\Realization[] realizations A collection of foreign models related via has many relation 
 * @property-read \Model\Order[] deliveryOrders A collection of foreign models related via has many relation 
 */
abstract class Delivery extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'deliveries';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'start' => array('db_type' => 'date'),
                                'serviceId' => array('db_type' => 'integer'),
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
                                'serviceId' => 'service_id',
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
                                    'service_id',
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
                                        'date_format:Y-m-d',
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

                                    'service_id' => array (
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
                                        'deliveryWindows' => '\\Model\\DeliveryWindow',
                                        'realizations' => '\\Model\\Realization',
                                        'deliveryOrders' => '\\Model\\Order',
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
                    'service_id' => 'service_id',
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
    public function deliveryWindows()
    {
        return $this->hasMany('\\Model\\DeliveryWindow', 'delivery_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function realizations()
    {
        return $this->hasMany('\\Model\\Realization', 'delivery_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveryOrders()
    {
        return $this->hasMany('\\Model\\Order', 'delivery_id');
    }

}
