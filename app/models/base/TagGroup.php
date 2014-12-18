<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model TagGroup
 * Automatically generated abstract model class
 *
 * @method \Model\TagGroup[] get() public static function get(array $columns = array()) returns an array of models TagGroup
 * @method null|\Model\TagGroup first() public static function first(array $columns = array()) returns model TagGroup
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $name
 * @property int $serviceId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Service|null service Foreign model related via many belongs to one relation 
 * @property-read \Model\Tag[] tags A collection of foreign models related via has many relation 
 */
abstract class TagGroup extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'tag_groups';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
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
                                'identifier' => 'identifier',
                                'name' => 'name',
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
                                    'identifier',
                                    'name',
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

                                    'identifier' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'unique',
                                            'tag_groups',
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

                                    'service_id' => array (
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
                                        'service' => '\\Model\\Service',
                                        'tags' => '\\Model\\Tag',
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
                    'service_id' => 'service_id',
                );
    }



    /* Model specific methods follows */


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
    public function tags()
    {
        return $this->hasMany('\\Model\\Tag', 'tag_group_id');
    }

}
