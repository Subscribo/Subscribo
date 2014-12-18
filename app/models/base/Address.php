<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Address
 * Automatically generated abstract model class
 *
 * @method \Model\Address[] get() public static function get(array $columns = array()) returns an array of models Address
 * @method null|\Model\Address first() public static function first(array $columns = array()) returns model Address
 *
 * @property int $id Primary key
 * @property int $type
 * @property string $firstLine
 * @property string $secondLine
 * @property string $street
 * @property string $house
 * @property string $stairway
 * @property string $floor
 * @property string $apartment
 * @property string $postCode
 * @property string $city Settlement name
 * @property string $district district of a city
 * @property string $province state/country subdivision
 * @property int $stateId e.g. US state
 * @property int $countryId
 * @property string $countryUnion
 * @property string $gpsLongitude
 * @property string $gpsLatitude
 * @property int $contactPhone Phone number in international format without leading + or 00 (another possibility would be to save it as string
 * @property string $deliveryInformation
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\State|null state Foreign model related via many belongs to one relation 
 * @property-read \Model\Country|null country Foreign model related via many belongs to one relation 
 * @property-read \Model\User[] users A collection of foreign models related via has many relation 
 * @property-read \Model\Contact[] homeAddressContacts A collection of foreign models related via has many relation 
 * @property-read \Model\Contact[] workAddressContacts A collection of foreign models related via has many relation 
 * @property-read \Model\BillingDetail[] billingDetails A collection of foreign models related via has many relation 
 * @property-read \Model\Bank[] banks A collection of foreign models related via has many relation 
 * @property-read \Model\Order[] shippingAddressOrders A collection of foreign models related via has many relation 
 */
abstract class Address extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'addresses';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'type' => array('db_type' => 'tinyinteger'),
                                'firstLine' => array('db_type' => 'varchar'),
                                'secondLine' => array('db_type' => 'varchar'),
                                'street' => array('db_type' => 'varchar'),
                                'house' => array('db_type' => 'varchar'),
                                'stairway' => array('db_type' => 'varchar'),
                                'floor' => array('db_type' => 'varchar'),
                                'apartment' => array('db_type' => 'varchar'),
                                'postCode' => array('db_type' => 'varchar'),
                                'city' => array('db_type' => 'varchar'),
                                'district' => array('db_type' => 'varchar'),
                                'province' => array('db_type' => 'varchar'),
                                'stateId' => array('db_type' => 'integer'),
                                'countryId' => array('db_type' => 'integer'),
                                'countryUnion' => array('db_type' => 'enum'),
                                'gpsLongitude' => array('db_type' => 'varchar'),
                                'gpsLatitude' => array('db_type' => 'varchar'),
                                'contactPhone' => array('db_type' => 'biginteger'),
                                'deliveryInformation' => array('db_type' => 'text'),
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
                                'firstLine' => 'first_line',
                                'secondLine' => 'second_line',
                                'street' => 'street',
                                'house' => 'house',
                                'stairway' => 'stairway',
                                'floor' => 'floor',
                                'apartment' => 'apartment',
                                'postCode' => 'post_code',
                                'city' => 'city',
                                'district' => 'district',
                                'province' => 'province',
                                'stateId' => 'state_id',
                                'countryId' => 'country_id',
                                'countryUnion' => 'country_union',
                                'gpsLongitude' => 'gps_longitude',
                                'gpsLatitude' => 'gps_latitude',
                                'contactPhone' => 'contact_phone',
                                'deliveryInformation' => 'delivery_information',
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
                                    'first_line',
                                    'second_line',
                                    'street',
                                    'house',
                                    'stairway',
                                    'floor',
                                    'apartment',
                                    'post_code',
                                    'city',
                                    'district',
                                    'province',
                                    'state_id',
                                    'country_id',
                                    'country_union',
                                    'gps_longitude',
                                    'gps_latitude',
                                    'contact_phone',
                                    'delivery_information',
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

                                    'first_line' => array (
                                        'max:255',
                                    ),

                                    'second_line' => array (
                                        'max:255',
                                    ),

                                    'street' => array (
                                        'max:255',
                                    ),

                                    'house' => array (
                                        'max:255',
                                    ),

                                    'stairway' => array (
                                        'max:255',
                                    ),

                                    'floor' => array (
                                        'max:255',
                                    ),

                                    'apartment' => array (
                                        'max:255',
                                    ),

                                    'post_code' => array (
                                        'max:255',
                                    ),

                                    'city' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'district' => array (
                                        'max:255',
                                    ),

                                    'province' => array (
                                        'max:255',
                                    ),

                                    'state_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'states',
                                            'id',
                                        ),
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

                                    'country_union' => array (
                                        array (
                                            'in',
                                            'EU',
                                        ),
                                    ),

                                    'gps_longitude' => array (
                                        'max:255',
                                    ),

                                    'gps_latitude' => array (
                                        'max:255',
                                    ),

                                    'contact_phone' => array (
                                        'integer',
                                        'between:0,18446744073709551615',
                                    ),

                                    'delivery_information' => array (
                                        'required',
                                        'max:65535',
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

                                    'state_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'country_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'country_union' => array (
                                        'non_printable_to_null',
                                    ),

                                    'contact_phone' => array (
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
                                        'state' => '\\Model\\State',
                                        'country' => '\\Model\\Country',
                                        'users' => '\\Model\\User',
                                        'homeAddressContacts' => '\\Model\\Contact',
                                        'workAddressContacts' => '\\Model\\Contact',
                                        'billingDetails' => '\\Model\\BillingDetail',
                                        'banks' => '\\Model\\Bank',
                                        'shippingAddressOrders' => '\\Model\\Order',
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
                    'first_line' => 'first_line',
                    'second_line' => 'second_line',
                    'street' => 'street',
                    'house' => 'house',
                    'stairway' => 'stairway',
                    'floor' => 'floor',
                    'apartment' => 'apartment',
                    'post_code' => 'post_code',
                    'city' => 'city',
                    'district' => 'district',
                    'province' => 'province',
                    'state_id' => 'state_id',
                    'country_id' => 'country_id',
                    'country_union' => 'country_union',
                    'gps_longitude' => 'gps_longitude',
                    'gps_latitude' => 'gps_latitude',
                    'contact_phone' => 'contact_phone',
                    'delivery_information' => 'delivery_information',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('\\Model\\State', 'state_id', null, 'state');
    }

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
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('\\Model\\User', 'default_delivery_address_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function homeAddressContacts()
    {
        return $this->hasMany('\\Model\\Contact', 'home_address_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAddressContacts()
    {
        return $this->hasMany('\\Model\\Contact', 'work_address_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billingDetails()
    {
        return $this->hasMany('\\Model\\BillingDetail', 'address_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany('\\Model\\Bank', 'address_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingAddressOrders()
    {
        return $this->hasMany('\\Model\\Order', 'shipping_address_id');
    }

}
