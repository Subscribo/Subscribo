<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Currency
 * Automatically generated abstract model class
 *
 * @method \Model\Currency[] get() public static function get(array $columns = array()) returns an array of models Currency
 * @method null|\Model\Currency first() public static function first(array $columns = array()) returns model Currency
 *
 * @property int $id Primary key
 * @property string $name
 * @property string $code
 * @property string $symbol
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Payment[] payments A collection of foreign models related via has many relation 
 */
abstract class Currency extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'currencies';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'name' => array('db_type' => 'varchar'),
                                'code' => array('db_type' => 'varchar'),
                                'symbol' => array('db_type' => 'varchar'),
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
                                'code' => 'code',
                                'symbol' => 'symbol',
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
                                    'code',
                                    'symbol',
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

                                    'code' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'symbol' => array (
                                        'required',
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
                    'name' => 'name',
                    'code' => 'code',
                    'symbol' => 'symbol',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('\\Model\\Payment', 'currency_id');
    }

}
