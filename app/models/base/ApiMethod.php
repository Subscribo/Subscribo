<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model ApiMethod
 * Automatically generated abstract model class
 *
 * @method \Model\ApiMethod[] get() public static function get(array $columns = array()) returns an array of models ApiMethod
 * @method null|\Model\ApiMethod first() public static function first(array $columns = array()) returns model ApiMethod
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $name human readable name
 * @property string $comment some description, what is it doing
 * @property int $apiId
 * @property bool $element true for element, false for collection
 * @property string $httpVerb
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Api|null api Foreign model related via many belongs to one relation 
 * @property-read \Model\AclRight[] aclRights A collection of foreign models related via has many relation 
 */
abstract class ApiMethod extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'api_methods';


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
                                'apiId' => array('db_type' => 'integer'),
                                'element' => array('db_type' => 'boolean'),
                                'httpVerb' => array('db_type' => 'enum'),
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
                                'apiId' => 'api_id',
                                'element' => 'element',
                                'httpVerb' => 'http_verb',
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
                                    'api_id',
                                    'element',
                                    'http_verb',
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
                                            'api_methods',
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

                                    'api_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'apis',
                                            'id',
                                        ),
                                    ),

                                    'element' => array (
                                        'required',
                                        'boolean',
                                    ),

                                    'http_verb' => array (
                                        'required',
                                        array (
                                            'in',
                                            'GET',
                                            'POST',
                                            'PUT',
                                            'DELETE',
                                            'OPTIONS',
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

                                    'api_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'element' => array (
                                        'non_printable_to_null',
                                    ),

                                    'http_verb' => array (
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
                                        'api' => '\\Model\\Api',
                                        'aclRights' => '\\Model\\AclRight',
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
                    'api_id' => 'api_id',
                    'element' => 'element',
                    'http_verb' => 'http_verb',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function api()
    {
        return $this->belongsTo('\\Model\\Api', 'api_id', null, 'api');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aclRights()
    {
        return $this->hasMany('\\Model\\AclRight', 'api_method_id');
    }

}
