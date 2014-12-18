<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model User
 * Automatically generated abstract model class
 *
 * @method \Model\User[] get() public static function get(array $columns = array()) returns an array of models User
 * @method null|\Model\User first() public static function first(array $columns = array()) returns model User
 *
 * @property int $id Primary key
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $type (we should have here the basic separation between administrators and customers)
 * @property string $rememberToken (and other possible technical fields allowing logging in)
 * @property bool $emailConfirmed
 * @property string $oauth
 * @property string $fbAccount
 * @property int $personId
 * @property int $defaultDeliveryAddressId
 * @property int $defaultBillingDetailsId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\AclGroup[] aclGroups A collection of foreign models related via many to many relation 
 * @property-read \Model\Person|null person Foreign model related via many belongs to one relation 
 * @property-read \Model\Address|null defaultDeliveryAddress Foreign model related via many belongs to one relation 
 * @property-read \Model\BillingDetail|null defaultBillingDetail Foreign model related via many belongs to one relation 
 * @property-read \Model\Account[] accounts A collection of foreign models related via has many relation 
 */
abstract class User extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'users';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'username' => array('db_type' => 'varchar'),
                                'email' => array('db_type' => 'varchar'),
                                'password' => array('db_type' => 'varchar'),
                                'type' => array('db_type' => 'enum'),
                                'rememberToken' => array('db_type' => 'varchar'),
                                'emailConfirmed' => array('db_type' => 'boolean'),
                                'oauth' => array('db_type' => 'varchar'),
                                'fbAccount' => array('db_type' => 'varchar'),
                                'personId' => array('db_type' => 'integer'),
                                'defaultDeliveryAddressId' => array('db_type' => 'integer'),
                                'defaultBillingDetailsId' => array('db_type' => 'integer'),
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
                                'username' => 'username',
                                'email' => 'email',
                                'password' => 'password',
                                'type' => 'type',
                                'rememberToken' => 'remember_token',
                                'emailConfirmed' => 'email_confirmed',
                                'oauth' => 'oauth',
                                'fbAccount' => 'fb_account',
                                'personId' => 'person_id',
                                'defaultDeliveryAddressId' => 'default_delivery_address_id',
                                'defaultBillingDetailsId' => 'default_billing_details_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @property array
     */
    protected $hidden = array('password', 'remember_token');


    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'username',
                                    'email',
                                    'password',
                                    'type',
                                    'remember_token',
                                    'email_confirmed',
                                    'oauth',
                                    'fb_account',
                                    'person_id',
                                    'default_delivery_address_id',
                                    'default_billing_details_id',
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

                                    'username' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'email' => array (
                                        'max:255',
                                    ),

                                    'password' => array (
                                        'max:255',
                                    ),

                                    'type' => array (
                                        'required',
                                        array (
                                            'in',
                                            'guest',
                                            'customer',
                                            'administrator',
                                            'superadmin',
                                        ),
                                    ),

                                    'remember_token' => array (
                                        'max:255',
                                    ),

                                    'email_confirmed' => array (
                                        'boolean',
                                    ),

                                    'oauth' => array (
                                        'max:255',
                                    ),

                                    'fb_account' => array (
                                        'max:255',
                                    ),

                                    'person_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'persons',
                                            'id',
                                        ),
                                    ),

                                    'default_delivery_address_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'addresses',
                                            'id',
                                        ),
                                    ),

                                    'default_billing_details_id' => array (
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

                                    'email_confirmed' => array (
                                        'non_printable_to_null',
                                    ),

                                    'person_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'default_delivery_address_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'default_billing_details_id' => array (
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
                                        'aclGroups' => '\\Model\\AclGroup',
                                        'person' => '\\Model\\Person',
                                        'defaultDeliveryAddress' => '\\Model\\Address',
                                        'defaultBillingDetail' => '\\Model\\BillingDetail',
                                        'accounts' => '\\Model\\Account',
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
                    'username' => 'username',
                    'email' => 'email',
                    'type' => 'type',
                    'email_confirmed' => 'email_confirmed',
                    'oauth' => 'oauth',
                    'fb_account' => 'fb_account',
                    'person_id' => 'person_id',
                    'default_delivery_address_id' => 'default_delivery_address_id',
                    'default_billing_details_id' => 'default_billing_details_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function aclGroups()
    {
        return $this->belongsToMany('\\Model\\AclGroup', 'acl_group_user', 'user_id', 'acl_group_id', 'aclGroups');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo('\\Model\\Person', 'person_id', null, 'person');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defaultDeliveryAddress()
    {
        return $this->belongsTo('\\Model\\Address', 'default_delivery_address_id', null, 'defaultDeliveryAddress');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defaultBillingDetail()
    {
        return $this->belongsTo('\\Model\\BillingDetail', 'default_billing_details_id', null, 'defaultBillingDetail');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany('\\Model\\Account', 'user_id');
    }

}
