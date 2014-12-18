<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Table
 * Automatically generated abstract model class
 *
 * @method \Model\Table[] get() public static function get(array $columns = array()) returns an array of models Table
 * @method null|\Model\Table first() public static function first(array $columns = array()) returns model Table
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API - actual table name
 * @property string $comment
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Translation[] translations A collection of foreign models related via has many relation 
 */
abstract class Table extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'tables';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'comment' => array('db_type' => 'varchar'),
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
                                'comment' => 'comment',
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
                                    'comment',
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
                                            'tables',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'comment' => array (
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
                                );

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array(
                                        'translations' => '\\Model\\Translation',
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
                    'comment' => 'comment',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany('\\Model\\Translation', 'table_id');
    }

}
