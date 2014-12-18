<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Bank
 * Automatically generated abstract model class
 *
 * @method \Model\Bank[] get() public static function get(array $columns = array()) returns an array of models Bank
 * @method null|\Model\Bank first() public static function first(array $columns = array()) returns model Bank
 *
 * @property int $id Primary key
 * @property string $name
 * @property string $bic
 * @property string $bankCode
 * @property int $countryId
 * @property int $addressId Address of the bank
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Country|null country Foreign model related via many belongs to one relation 
 * @property-read \Model\Address|null address Foreign model related via many belongs to one relation 
 * @property-read \Model\BillingDetail[] billingDetails A collection of foreign models related via has many relation 
 */
abstract class Bank extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'banks';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'name' => array('db_type' => 'varchar'),
                                'bic' => array('db_type' => 'varchar'),
                                'bankCode' => array('db_type' => 'varchar'),
                                'countryId' => array('db_type' => 'integer'),
                                'addressId' => array('db_type' => 'integer'),
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
                                'name' => 'name',
                                'bic' => 'bic',
                                'bankCode' => 'bank_code',
                                'countryId' => 'country_id',
                                'addressId' => 'address_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'name',
                                    'bic',
                                    'bank_code',
                                    'country_id',
                                    'address_id',
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

                                    'name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'bic' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'bank_code' => array (
                                        'max:255',
                                    ),

                                    'country_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'countries',
                                            'id',
                                        ),
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

                                    'country_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'address_id' => array (
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
                                        'country' => '\\Model\\Country',
                                        'address' => '\\Model\\Address',
                                        'billingDetails' => '\\Model\\BillingDetail',
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
                    'name' => 'name',
                    'bic' => 'bic',
                    'bank_code' => 'bank_code',
                    'country_id' => 'country_id',
                    'address_id' => 'address_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('\\Model\\Country', 'country_id', null, 'country');
    }

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
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billingDetails()
    {
        return $this->hasMany('\\Model\\BillingDetail', 'bank_id');
    }

}
