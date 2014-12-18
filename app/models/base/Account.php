<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Account
 * Automatically generated abstract model class
 *
 * @method \Model\Account[] get() public static function get(array $columns = array()) returns an array of models Account
 * @method null|\Model\Account first() public static function first(array $columns = array()) returns model Account
 *
 * @property int $id
 * @property int $userId
 * @property int $serviceId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\User|null user Foreign model related via many belongs to one relation 
 * @property-read \Model\Service|null service Foreign model related via many belongs to one relation 
 * @property-read \Model\Subscription[] subscriptions A collection of foreign models related via has many relation 
 * @property-read \Model\Order[] accountOrders A collection of foreign models related via has many relation 
 */
abstract class Account extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'accounts';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'userId' => array('db_type' => 'integer'),
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
                                'userId' => 'user_id',
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
                                    'user_id',
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

                                    'user_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'users',
                                            'id',
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

                                    'user_id' => array (
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
                                        'user' => '\\Model\\User',
                                        'service' => '\\Model\\Service',
                                        'subscriptions' => '\\Model\\Subscription',
                                        'accountOrders' => '\\Model\\Order',
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
                    'user_id' => 'user_id',
                    'service_id' => 'service_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\\Model\\User', 'user_id', null, 'user');
    }

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
    public function subscriptions()
    {
        return $this->hasMany('\\Model\\Subscription', 'account_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accountOrders()
    {
        return $this->hasMany('\\Model\\Order', 'account_id');
    }

}
