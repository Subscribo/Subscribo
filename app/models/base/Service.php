<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Service
 * Automatically generated abstract model class
 *
 * @method \Model\Service[] get() public static function get(array $columns = array()) returns an array of models Service
 * @method null|\Model\Service first() public static function first(array $columns = array()) returns model Service
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $name human readable name
 * @property string $comment some technical comment to particular row, such as “do not use now, just for testing”
 * @property string $url
 * @property int $defaultLanguageId
 * @property int $operatorId operators table does not exist in current version of the schema, so the relation type is no_relation
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\ServicePool[] servicePools A collection of foreign models related via many to many relation 
 * @property-read \Model\Api[] apis A collection of foreign models related via many to many relation 
 * @property-read \Model\Language[] availableLanguages A collection of foreign models related via many to many relation 
 * @property-read \Model\Language|null defaultLanguage Foreign model related via many belongs to one relation 
 * @property-read \Model\Account[] accounts A collection of foreign models related via has many relation 
 * @property-read \Model\Product[] products A collection of foreign models related via has many relation 
 * @property-read \Model\Delivery[] deliveries A collection of foreign models related via has many relation 
 * @property-read \Model\DeliveryWindowType[] deliveryWindowTypes A collection of foreign models related via has many relation 
 * @property-read \Model\TagGroup[] tagGroups A collection of foreign models related via has many relation 
 */
abstract class Service extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'services';


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
                                'url' => array('db_type' => 'varchar'),
                                'defaultLanguageId' => array('db_type' => 'integer'),
                                'operatorId' => array('db_type' => 'integer'),
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
                                'url' => 'url',
                                'defaultLanguageId' => 'default_language_id',
                                'operatorId' => 'operator_id',
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
                                    'url',
                                    'default_language_id',
                                    'operator_id',
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
                                            'services',
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

                                    'url' => array (
                                        'max:255',
                                    ),

                                    'default_language_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'languages',
                                            'id',
                                        ),
                                    ),

                                    'operator_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
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

                                    'default_language_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'operator_id' => array (
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
                                        'servicePools' => '\\Model\\ServicePool',
                                        'apis' => '\\Model\\Api',
                                        'availableLanguages' => '\\Model\\Language',
                                        'defaultLanguage' => '\\Model\\Language',
                                        'accounts' => '\\Model\\Account',
                                        'products' => '\\Model\\Product',
                                        'deliveries' => '\\Model\\Delivery',
                                        'deliveryWindowTypes' => '\\Model\\DeliveryWindowType',
                                        'tagGroups' => '\\Model\\TagGroup',
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
                    'url' => 'url',
                    'default_language_id' => 'default_language_id',
                    'operator_id' => 'operator_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function servicePools()
    {
        return $this->belongsToMany('\\Model\\ServicePool', 'service_service_pool', 'service_id', 'service_pool_id', 'servicePools');
    }

    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apis()
    {
        return $this->belongsToMany('\\Model\\Api', 'api_service', 'service_id', 'api_id', 'apis');
    }

    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function availableLanguages()
    {
        return $this->belongsToMany('\\Model\\Language', 'language_service', 'service_id', 'language_id', 'languages');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defaultLanguage()
    {
        return $this->belongsTo('\\Model\\Language', 'default_language_id', null, 'defaultLanguage');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany('\\Model\\Account', 'service_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('\\Model\\Product', 'service_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveries()
    {
        return $this->hasMany('\\Model\\Delivery', 'service_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveryWindowTypes()
    {
        return $this->hasMany('\\Model\\DeliveryWindowType', 'service_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagGroups()
    {
        return $this->hasMany('\\Model\\TagGroup', 'service_id');
    }

}
