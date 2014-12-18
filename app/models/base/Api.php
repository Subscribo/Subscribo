<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Api
 * Automatically generated abstract model class
 *
 * @method \Model\Api[] get() public static function get(array $columns = array()) returns an array of models Api
 * @method null|\Model\Api first() public static function first(array $columns = array()) returns model Api
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $name human readable name
 * @property string $comment
 * @property int $version
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Service[] services A collection of foreign models related via many to many relation 
 * @property-read \Model\ApiMethod[] apiMethods A collection of foreign models related via has many relation 
 */
abstract class Api extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'apis';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
                                'comment' => array('db_type' => 'varchar'),
                                'version' => array('db_type' => 'integer'),
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
                                'identifier' => 'identifier',
                                'name' => 'name',
                                'comment' => 'comment',
                                'version' => 'version',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'identifier',
                                    'name',
                                    'comment',
                                    'version',
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

                                    'identifier' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'unique',
                                            'apis',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'comment' => array (
                                        'max:255',
                                    ),

                                    'version' => array (
                                        'required',
                                        'integer',
                                        'between:-2147483648,2147483647',
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

                                    'version' => array (
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
                                        'services' => '\\Model\\Service',
                                        'apiMethods' => '\\Model\\ApiMethod',
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
                    'identifier' => 'identifier',
                    'name' => 'name',
                    'comment' => 'comment',
                    'version' => 'version',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany('\\Model\\Service', 'api_service', 'api_id', 'service_id', 'services');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apiMethods()
    {
        return $this->hasMany('\\Model\\ApiMethod', 'api_id');
    }

}
