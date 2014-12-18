<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Contact
 * Automatically generated abstract model class
 *
 * @method \Model\Contact[] get() public static function get(array $columns = array()) returns an array of models Contact
 * @method null|\Model\Contact first() public static function first(array $columns = array()) returns model Contact
 *
 * @property int $id Primary key
 * @property int $mobilePhoneNumber Phone number in international format without leading + or 00 (another possibility would be to save it as string
 * @property int $landlinePhoneNumber
 * @property int $homeAddressId
 * @property int $workAddressId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Address|null homeAddress Foreign model related via many belongs to one relation 
 * @property-read \Model\Address|null workAddress Foreign model related via many belongs to one relation 
 * @property-read \Model\Person[] persons A collection of foreign models related via has many relation 
 */
abstract class Contact extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'contacts';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'mobilePhoneNumber' => array('db_type' => 'biginteger'),
                                'landlinePhoneNumber' => array('db_type' => 'biginteger'),
                                'homeAddressId' => array('db_type' => 'integer'),
                                'workAddressId' => array('db_type' => 'integer'),
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
                                'mobilePhoneNumber' => 'mobile_phone_number',
                                'landlinePhoneNumber' => 'landline_phone_number',
                                'homeAddressId' => 'home_address_id',
                                'workAddressId' => 'work_address_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'mobile_phone_number',
                                    'landline_phone_number',
                                    'home_address_id',
                                    'work_address_id',
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

                                    'mobile_phone_number' => array (
                                        'integer',
                                        'between:0,18446744073709551615',
                                    ),

                                    'landline_phone_number' => array (
                                        'integer',
                                        'between:0,18446744073709551615',
                                    ),

                                    'home_address_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'addresses',
                                            'id',
                                        ),
                                    ),

                                    'work_address_id' => array (
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

                                    'mobile_phone_number' => array (
                                        'non_printable_to_null',
                                    ),

                                    'landline_phone_number' => array (
                                        'non_printable_to_null',
                                    ),

                                    'home_address_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'work_address_id' => array (
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
                                        'homeAddress' => '\\Model\\Address',
                                        'workAddress' => '\\Model\\Address',
                                        'persons' => '\\Model\\Person',
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
                    'mobile_phone_number' => 'mobile_phone_number',
                    'landline_phone_number' => 'landline_phone_number',
                    'home_address_id' => 'home_address_id',
                    'work_address_id' => 'work_address_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function homeAddress()
    {
        return $this->belongsTo('\\Model\\Address', 'home_address_id', null, 'homeAddress');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workAddress()
    {
        return $this->belongsTo('\\Model\\Address', 'work_address_id', null, 'workAddress');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function persons()
    {
        return $this->hasMany('\\Model\\Person', 'contact_id');
    }

}
