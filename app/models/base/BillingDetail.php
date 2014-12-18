<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model BillingDetail
 * Automatically generated abstract model class
 *
 * @method \Model\BillingDetail[] get() public static function get(array $columns = array()) returns an array of models BillingDetail
 * @method null|\Model\BillingDetail first() public static function first(array $columns = array()) returns model BillingDetail
 *
 * @property int $id Primary key
 * @property int $type
 * @property int $addressId
 * @property string $accountNo
 * @property int $bankId
 * @property string $iban
 * @property string $bic
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Address|null address Foreign model related via many belongs to one relation 
 * @property-read \Model\Bank|null bank Foreign model related via many belongs to one relation 
 * @property-read \Model\User[] users A collection of foreign models related via has many relation 
 * @property-read \Model\Payment[] payments A collection of foreign models related via has many relation 
 */
abstract class BillingDetail extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'billing_details';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'type' => array('db_type' => 'tinyinteger'),
                                'addressId' => array('db_type' => 'integer'),
                                'accountNo' => array('db_type' => 'varchar'),
                                'bankId' => array('db_type' => 'integer'),
                                'iban' => array('db_type' => 'varchar'),
                                'bic' => array('db_type' => 'varchar'),
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
                                'addressId' => 'address_id',
                                'accountNo' => 'account_no',
                                'bankId' => 'bank_id',
                                'iban' => 'iban',
                                'bic' => 'bic',
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
                                    'address_id',
                                    'account_no',
                                    'bank_id',
                                    'iban',
                                    'bic',
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

                                    'address_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'addresses',
                                            'id',
                                        ),
                                    ),

                                    'account_no' => array (
                                        'max:255',
                                    ),

                                    'bank_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'banks',
                                            'id',
                                        ),
                                    ),

                                    'iban' => array (
                                        'max:255',
                                    ),

                                    'bic' => array (
                                        'max:255',
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

                                    'address_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'bank_id' => array (
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
                                        'address' => '\\Model\\Address',
                                        'bank' => '\\Model\\Bank',
                                        'users' => '\\Model\\User',
                                        'payments' => '\\Model\\Payment',
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
                    'address_id' => 'address_id',
                    'account_no' => 'account_no',
                    'bank_id' => 'bank_id',
                    'iban' => 'iban',
                    'bic' => 'bic',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo('\\Model\\Address', 'address_id', null, 'address');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank()
    {
        return $this->belongsTo('\\Model\\Bank', 'bank_id', null, 'bank');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('\\Model\\User', 'default_billing_details_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('\\Model\\Payment', 'billing_detail_id');
    }

}
