<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Veto
 * Automatically generated abstract model class
 *
 * @method \Model\Veto[] get() public static function get(array $columns = array()) returns an array of models Veto
 * @method null|\Model\Veto first() public static function first(array $columns = array()) returns model Veto
 *
 * @property int $id Primary key
 * @property string $start
 * @property string $end
 * @property int $subscriptionId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Subscription|null subscription Foreign model related via many belongs to one relation 
 */
abstract class Veto extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'vetos';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'start' => array('db_type' => 'date'),
                                'end' => array('db_type' => 'date'),
                                'subscriptionId' => array('db_type' => 'integer'),
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
                                'subscriptionId' => 'subscription_id',
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
                                    'subscription_id',
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

                                    'end' => array (
                                        'required',
                                        'date_format:Y-m-d',
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

                                    'subscription_id' => array (
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
                    'subscription_id' => 'subscription_id',
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

}
