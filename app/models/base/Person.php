<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Person
 * Automatically generated abstract model class
 *
 * @method \Model\Person[] get() public static function get(array $columns = array()) returns an array of models Person
 * @method null|\Model\Person first() public static function first(array $columns = array()) returns model Person
 *
 * @property int $id Primary key
 * @property string $salutation
 * @property string $prefix
 * @property string $firstName
 * @property string $middleNames
 * @property string $infix
 * @property string $lastName
 * @property string $suffix
 * @property string $gender
 * @property string $dateOfBirth
 * @property int $contactId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Contact|null contact Foreign model related via many belongs to one relation 
 * @property-read \Model\User[] users A collection of foreign models related via has many relation 
 */
abstract class Person extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'persons';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'salutation' => array('db_type' => 'varchar'),
                                'prefix' => array('db_type' => 'varchar'),
                                'firstName' => array('db_type' => 'varchar'),
                                'middleNames' => array('db_type' => 'varchar'),
                                'infix' => array('db_type' => 'varchar'),
                                'lastName' => array('db_type' => 'varchar'),
                                'suffix' => array('db_type' => 'varchar'),
                                'gender' => array('db_type' => 'enum'),
                                'dateOfBirth' => array('db_type' => 'date'),
                                'contactId' => array('db_type' => 'integer'),
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
                                'salutation' => 'salutation',
                                'prefix' => 'prefix',
                                'firstName' => 'first_name',
                                'middleNames' => 'middle_names',
                                'infix' => 'infix',
                                'lastName' => 'last_name',
                                'suffix' => 'suffix',
                                'gender' => 'gender',
                                'dateOfBirth' => 'date_of_birth',
                                'contactId' => 'contact_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'salutation',
                                    'prefix',
                                    'first_name',
                                    'middle_names',
                                    'infix',
                                    'last_name',
                                    'suffix',
                                    'gender',
                                    'date_of_birth',
                                    'contact_id',
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

                                    'salutation' => array (
                                        'max:255',
                                    ),

                                    'prefix' => array (
                                        'max:255',
                                    ),

                                    'first_name' => array (
                                        'max:255',
                                    ),

                                    'middle_names' => array (
                                        'max:255',
                                    ),

                                    'infix' => array (
                                        'max:255',
                                    ),

                                    'last_name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'suffix' => array (
                                        'max:255',
                                    ),

                                    'gender' => array (
                                        'required',
                                        array (
                                            'in',
                                            'man',
                                            'woman',
                                        ),
                                    ),

                                    'date_of_birth' => array (
                                        'date_format:Y-m-d',
                                    ),

                                    'contact_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'contacts',
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

                                    'gender' => array (
                                        'non_printable_to_null',
                                    ),

                                    'date_of_birth' => array (
                                        'non_printable_to_null',
                                    ),

                                    'contact_id' => array (
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
                                        'contact' => '\\Model\\Contact',
                                        'users' => '\\Model\\User',
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
                    'salutation' => 'salutation',
                    'prefix' => 'prefix',
                    'first_name' => 'first_name',
                    'middle_names' => 'middle_names',
                    'infix' => 'infix',
                    'last_name' => 'last_name',
                    'suffix' => 'suffix',
                    'gender' => 'gender',
                    'date_of_birth' => 'date_of_birth',
                    'contact_id' => 'contact_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo('\\Model\\Contact', 'contact_id', null, 'contact');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('\\Model\\User', 'person_id');
    }

}
